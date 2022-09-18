<?php

namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\User;
use App\Models\Source;
use Illuminate\Support\Str;

class SourcesController extends Controller
{
    public function view()
    {
        $id = current_project()->id;

        $data['project'] = Project::with(['i18n','sources'])->find($id);
        $data['sources'] = Source::get();

        return Inertia::render('Projects/Sources', $data);
    }

    public function toggle()
    {
        $id = request('id');
        $project = current_project();

        if($project->sources->contains($id)){
            $project->sources()->detach($id);
        } else {
            $project->sources()->attach($id, ['is_active' => true]);
        } 
       
        /*
        $source = $project->sources->where('id', $id)->first();
        $source->pivot->increment('count');
        */

        return redirect()->back()->with('status','Saved');
    }

    public function store()
    {
        $obj = request('source');
        $project = current_project();
        $id = $obj['id'];

        if($project->sources->contains($id)){
            $project->sources()->detach($id);
        } else {
            $project->sources()->attach($id, ['is_active' => true]);
        }        

        return redirect()->back()->with('status','New source created');
    }
}