<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureProjectRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $roles = ['superadmin','admin','editor','contributor'];
        $key = array_search($role, $roles);
        $user = $request->user()->role['level'];

        if( $user <= $key || $request->user()->is_admin ):
            return $next($request);
        else:
            return redirect('/projects/view')->withErrors(['You have no permission to access this page']);
        endif;
    }
}
