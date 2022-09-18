<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\Project;
use Illuminate\Support\Facades\Hash;

class EnsureTokenIsValid
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
        $pid = $request->input('project_id');
        $project = Project::where('ucode', $pid)->first();
        $token = $request->bearerToken();

        if($project && Hash::check($token, $project->api_token)):
            return $next($request);
        else:
            return abort(403);
        endif;
    }
}
