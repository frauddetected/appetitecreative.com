<?php

namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\Analytics;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('project.role:superadmin');
    }

    public function view()
    {    
        $id = current_project()->id;
        
        $data['module'] = project_module('analytics');
        $data['project'] = Project::with('analytics')->find($id);

        return Inertia::render('Projects/Analytics', $data);
    }

    public function store()
    {
        $analytics = Analytics::firstOrNew([
            'project_id' => current_project()->id
        ]);

        $analytics->platform = request('platform');
        $analytics->details = json_encode(request('details'));

        if($analytics->save()) {
            return redirect()->back()->with('status','Analytics settings updated');
        }
    }
}