<?php

namespace App\Http\Controllers\Sharing;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\Analytics;

class MainController extends Controller
{
    function index($project, $selfie)
    {    
        $data['project'] = $project = Project::where('ucode', $project)->with('sharing')->first();
        $data['locale'] = request('locale') ?? key($project->sharing->messages->share);
        $data['selfie'] = $selfie;

        return view('sharing.selfie', $data);
    }
}