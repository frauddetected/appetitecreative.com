<?php

use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\User;

if(!function_exists('current_project')){
    function current_project(){
        return Project::with(['users','owner'])->find(Auth::user()->current_project_id);
    }
}

function project_role()
{
    $project = Project::find(Auth::user()->current_project_id);
    $pid = Auth::user()->current_project_id;
    
    if($project->user_id == Auth::user()->id || Auth::user()->is_admin):
        return 'superadmin';
    else:
        $p = Auth::user()->otherProjects->where('pivot.project_id', $pid)->first();
        if($p) return $p->pivot->role;
    endif;
}

function project_module($module){
    return current_project()->hasModule($module);
}