<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureProjectAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        $condition = in_array(current_project()->id, $request->user()->projects->pluck('id')->toArray());
        
        if( $condition || $request->user()->is_admin ):
            return $next($request);
        else:
            abort(403);
        endif;     
    }
}
