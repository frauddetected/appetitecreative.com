<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;

class UserController extends Controller
{
    public function list(){
        if(Auth::user()->is_admin != 1){
            return redirect()->to('/')->with("status","You don't have permission to access this page.");
        }
        return Inertia::render('User/List');
    }

    public function manageList(){
        $start = request()->input('start', 0); // Get the 'start' parameter from the request, default to 0
        $length = request()->input('length', 10); // Get the 'length' parameter from the request, default to 10
        
        $users = User::select('users.id', 'users.email', 'users.name', 'project_user.role as user_role','users.created_at', 'users.overwrite_subscription','subscriptions.stripe_plan')
                    ->leftJoin('project_user', function ($join){
                        $join->on('project_user.user_id','=','users.id');
                    })
                    ->leftJoin('subscriptions', function ($join){
                        $join->on('subscriptions.user_id','=','users.id');
                    })
                    ->where('users.is_admin',0);

        $filter = request()->get('search');
        $search = (isset($filter['value'])) ? $filter['value'] : false;

        if($search){
            $users = $users->where(function ($query) use($search){
                $query->where('users.name','like','%'.$search.'%')
                        ->orWhere('users.email','like','%'.$search.'%');
            });
        }

        $recordsTotal = $users->count();
        // $users = $users->orderBy('users.id','DESC')->skip($start) // Apply the 'start' offset
        //                     ->take($length) // Apply the 'length' limit
        //                     ->get(); // Retrieve the data

        $users = $users->orderBy('users.id','DESC');
        if($length != -1){
            $users = $users->skip($start) // Apply the 'start' offset
            ->take($length); // Apply the 'length' limit
        }
        
        $users = $users->get(); // Retrieve the data
        $data = [];
        foreach ($users as $user) {
            $plan_type = '-';
            if($user->stripe_plan == env('STRIPE_PRICE_SILVER')){
                $plan_type = 'Silver';
            }
            elseif($user->stripe_plan == env('STRIPE_PRICE_BRONZE_MONTHLY')){
                $plan_type = 'Bronze';
            }
            elseif($user->stripe_plan == env('STRIPE_PRICE_BRONZE_GOLD')){
                $plan_type = 'Gold';
            }
            // Add the data for the row, including the "View" button
            $data[] = [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->user_role,
                'created_at' => date('Y-m-d H:i:s', strtotime($user->created_at)),
                'overwrite_subscription' => $user->overwrite_subscription,
                'plan_type' => $plan_type
            ];
        }
                            
        return response()->json([
            'data' => $data,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
        ]);
    }

    public function canSubscription(){

        $getId = request()->id;
        $user = User::find($getId);
        if( $user->overwrite_subscription == 'yes' ){
            $user->overwrite_subscription = 'no';
            $msg = 'User has been unsubscribed.';
        }
        else{
            $user->overwrite_subscription = 'yes';
            $msg = 'User has been subscribed.';
        }
        $user->save();
        return redirect()->back()->with('status', $msg);
    }
    
    public function userDelete(){
        $getId = request()->user_id;
        $user = User::find($getId);
        if ($user) {
            $user->delete();
            $msg = 'User has been deleted.';
        } else {
            $msg = 'User not found or could not be deleted.';
        }
        return redirect()->back()->with('status', $msg);

    }
}
