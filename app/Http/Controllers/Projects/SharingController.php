<?php

namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\User;
use App\Models\Sharing;
use Illuminate\Support\Str;

class SharingController extends Controller
{
    public function view()
    {
        $id = current_project()->id;   
        $data['project'] = Project::with(['i18n','sharing'])->find($id);

        return Inertia::render('Projects/Sharing', $data);

    }

    public function store()
    {
        $id = current_project()->id;
        
        $sharing = Sharing::firstOrNew([
            'project_id' => $id
        ]);

        $sharing->style = json_encode(request('style'));
        $sharing->messages = json_encode(request('messages'));

        if($sharing->save()){
            return redirect()->back()->with('status','Sharing settings has been saved');
        }
    }
}