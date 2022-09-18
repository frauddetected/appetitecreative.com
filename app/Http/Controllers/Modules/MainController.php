<?php

namespace App\Http\Controllers\Modules;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Project;
use App\Models\User;

class MainController extends Controller
{
    function update()
    {    
        $pid = current_project()->id;

        $module = Module::firstOrNew([
            'name' => request('name'),
            'project_id' => $pid
        ]);

        $module->is_active = request('enable');
        $module->save();

        $msg = 'This module has been successfully '.($module->is_active==true ? 'enabled' : 'disabled');

        return redirect()->back()->with('status', $msg);
    }
}