<?php

namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
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

    public function plausible()
    {
        $token = "u6_qkKV_om_JOe0Eku4qXZue4-yOp9byArtUvxwXBILGmU29kZYBUngyZSyXVVqz";
        
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                        ->attach('domain', 'test-domain.com')
                        ->attach('timezone', 'Europe/London')
                        ->post('https://analytics.appetitecreative.com/api/v1/sites');

        if ($response->ok()) {
            
            $response2 = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                            ->attach('site_id', 'test-domain.com')
                            ->attach('name', 'Wordpress')
                            ->put('https://plausible.io/api/v1/sites/shared-links');

            $responseData = $response->json();
            return $responseData;

        } else {
            return $response->json();
        }
    }

    public function store()
    {
        $analytics = Analytics::firstOrNew([
            'project_id' => current_project()->id
        ]);

        $analytics->platform = request('platform');
        $analytics->details = json_encode(request('details'));

        if($analytics->save()) {
            return redirect()->back()->with('status','Analytic settings updated');
        }
    }
}