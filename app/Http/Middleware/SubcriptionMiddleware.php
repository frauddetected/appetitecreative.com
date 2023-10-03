<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Redirector;
use DB;

class SubcriptionMiddleware
{
    protected $redirector;
    /**
     * Create the middleware.
     *
     * @return void
     */

     public function __construct(Redirector $redirector)
     {
         $this->redirector = $redirector;
     }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $redirectUrl = '/billing';
        $redirectProfileUrl = '/user/profile';
        $isFirstPlanRedirect = false;
        $date = date('Y-m-d H:i:s');
        $url = url()->current();
        $qrCodePermission = true;
        $remainingQrCode = 0;
        if($user && !Auth::user()->is_admin && Auth::user()->role['name'] != 'contributor' && Auth::user()->overwrite_subscription == 'no'){
            $userId = Auth::user()->id;
            
            if(strpos($url, "billing") !== false || strpos($url, "user/profile") !== false || strpos($url, "logout") !== false || strpos($url, "/contact") !== false || strpos($url, "/spark/") !== false){
                return $next($request);
            }
            else{
                $isFirstPlanRedirect = true;
            }

            $isUserSubscribed = DB::table('subscriptions')->where('user_id', $userId)->where('stripe_status','active')->first();
            $totalQrGenerate = 0;
            if($isUserSubscribed){
                $isFirstPlanRedirect = false;
                if($isUserSubscribed->stripe_plan == env('STRIPE_PRICE_SILVER')){
                    $totalQrGenerate =0;
                }
                elseif($isUserSubscribed->stripe_plan == env('STRIPE_PRICE_BRONZE_MONTHLY')){
                    $totalQrGenerate =25;
                }
                elseif($isUserSubscribed->stripe_plan == env('STRIPE_PRICE_BRONZE_GOLD')){
                    $totalQrGenerate =0;
                }
            }
            $getEditorQrCode = DB::table('qrcodes')->where('created_by', $userId)->count();
            if($getEditorQrCode){
               if(($getEditorQrCode >= $totalQrGenerate) && ($totalQrGenerate != -1)){
                $qrCodePermission = false;
               }
               $remainingQrCode = ($totalQrGenerate - $getEditorQrCode);
            }
        }
        $request->attributes->set('qrCodePermission', $qrCodePermission);
        $request->attributes->set('remainingQrCode', $remainingQrCode);
        
        if($isFirstPlanRedirect){
            return $this->redirector->to($redirectProfileUrl);
        }
        return $next($request);
    }
}