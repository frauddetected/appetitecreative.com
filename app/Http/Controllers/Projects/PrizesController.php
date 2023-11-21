<?php

namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\Prize;
use App\Models\User;

class PrizesController extends Controller
{
    public function view()
    {
        $id = current_project()->id;

        $data['module'] = project_module('prizes');
        $data['project'] = Project::with(['i18n','sources','prizes'])
        ->whereHas('qr', function ($query) {
            $query->where('is_unique', 0);
        })
        ->find($id);

        return Inertia::render('Projects/Prizes', $data);
    }

    public function store()
    {
        $id = current_project()->id;

        $prize = new Prize;
        $prize->title = request('title');
        $prize->periodicity = request('periodicity');
        $prize->limit = request('limit');
        $prize->project_id = $id;

        if($prize->save()){
            return redirect()->back()->with('status','Prize added successfully.');
        }
    }

    public function actions()
   {
        if(request('delPrize')):
            Prize::find(request('delPrize'))->delete();
            return redirect()->back()->with('status','Prize deleted successfully!');
        endif;
    } 
}