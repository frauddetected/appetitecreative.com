<?php

namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\User;
use App\Models\I18N;

class I18NController extends Controller
{
    public function view()
    {
        $id = current_project()->id;
        $data['project'] = Project::with('i18n')->find($id);

        return Inertia::render('Projects/i18n', $data);
    }

    public function store()
    {
        $i18n = I18N::firstOrNew([
            'project_id' => current_project()->id
        ]);

        $i18n->countries = json_encode(request('countries'));
        $i18n->languages = json_encode(request('languages'));

        if($i18n->save()){
            return redirect()->back()->with('status','i18n settings has been saved');
        }
    }

}