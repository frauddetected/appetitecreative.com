<?php

namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use DB;
use App\Models\Project;
use App\Models\User;
use App\Notifications\AddedToProject;
use App\Mail\NewAccountCreated;
use App\Models\LogAction;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\QueryBuilder;
use Schema;

class MainController extends Controller
{
    public function __construct()
    {
        $this->middleware('project.access');
        $this->middleware('auth.admin')->only(['create','store','updateToken','addMember']);
    }

    public function view()
    {        
        $id = current_project()->id;
        
        $roles = [
            'admin' => [
                'permissions' => ['create','read','update','delete'],
                'description' => 'Administrator users can perform any action.'
            ],
            'editor' => [
                'permissions' => ['create','read','update'],
                'description' => 'Editor users have the ability to read, create, and update.'
            ],
            'contributor' => [
                'permissions' => ['read'],
                'description' => 'Contributor users have the ability to read.'
            ],
        ];

        $data['availableRoles'] = $roles;
        $data['project'] = current_project();
        $data['token'] = session('token');

        return Inertia::render('Projects/Show', $data);
    }

    public function viewLogs()
    {
        /*
        Schema::table('log_action', function($table){
            $table->index('name');
        });
        */

        $data['logs'] = QueryBuilder::for(current_project()->logs())
        ->allowedFilters(['name','values'])
        ->orderBy('id','desc')
        ->paginate(30);

        $chart = current_project()->logs()
        ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as views'))
        ->groupBy('date')
        ->get();

        $dailyEvents = [];

        $chart->each(function($c) use (&$dailyEvents){
            $dailyEvents[$c->date] = $c->views;
        });

        $data['chart'] = $dailyEvents;
        $data['filter'] = request('filter');
        $data['page'] = request('page') ?? 1;

        return Inertia::render('Projects/Logs', $data);
    }

    public function updateDetails()
    {
        $project = Project::find(current_project()->id);
        $project->name = request('name');
        $project->ends_at = request('ends_at');

        if(Auth::user()->is_admin):
            $project->controller = request('controller');
            $project->domain = request('domain');
        endif;

        if($project->save()):
            return redirect()->back()->with('status', 'Details updated');
        endif;
    }

    public function current($id = null)
    {
        if(!$id) $id = request('id');

        $condition = in_array($id, Auth::user()->projects->pluck('id')->toArray());
        if(!$condition){
            return redirect()->to('/')->withErrors(array('You cannot do that'));
        }
        
        Auth::user()->forceFill([
            'current_project_id' => $id
        ])->save();

        $name = Project::find($id)->name;

        return redirect()->to('/')->with("status","You are now looking at {$name}");
    }

    public function resetData()
    {
        $project = Project::find(current_project()->id);

        current_project()->logs()->delete();
        current_project()->users()->delete();
        current_project()->participants()->delete();
        current_project()->members()->delete();
        current_project()->leaderboard()->delete();

        return redirect()->back()->with("status","Data has been reset");
    }

    public function toggleLive()
    {
        $current = current_project();

        if($current->is_test == true):
            $p = $current->live_project;
            return $this->current($p->id);
        else:

            if($current->test_project):
                return $this->current($current->test_project->id);
            else:
                $p = new Project;
                $p->user_id = Auth::user()->id;
                $p->name = $current->name;
                $p->ucode = Str::random(8);
                $p->api_token = Hash::make(Str::random(60));
                $p->is_test = true;
                $p->parent_id = $current->id;
                $p->save();
    
                Auth::user()->forceFill([
                    'current_project_id' => $p->id
                ])->save();
    
                return redirect()->to('/')->with("status","Test project created and toggled");
            endif;

        endif;
    }

    public function create()
    {
        return Inertia::render('Projects/Create');
    }

    function store(Request $request)
    {
        $request->validateWithBag('createProject', [
            'name' => ['required']
        ]);

        $project = new Project;
        $project->user_id = Auth::user()->id;
        $project->name = $request->input('name');
        $project->ucode = Str::random(8);
        $project->api_token = Hash::make(Str::random(60));
        
        if($project->save()):
            return redirect()->route('projects.view', $project->id)->with('status','Project created');
        else:
            return redirect()->back()->withErrors(['Project could not be created']);
        endif;
    }

    function updateToken()
    {
        $id = current_project()->id;
        $token = Str::random(30);
        
        current_project()->forceFill([
            'api_token' => Hash::make($token)
        ])->save();
        
        return redirect()->back()->with([
            'token' => $token,
            'status' => 'Token created. Save it! It will only be shown once.'
        ]);
    }

    public function editMemberRole()
    {
        if(project_role() == 'superadmin'):

            $user = User::find(request('id'));
            $pid = current_project()->id;
            $user->otherProjects()->sync([$pid => ['role' => request('role')]]);
            
            return redirect()->back()->with('status', 'Member role updated');
        
        else:
            
            return redirect()->back()->withErrors(["You can't edit member roles"]);

        endif;
    }

    public function addMember()
    {
        $pid = current_project()->id;
        $project = current_project();

        if(project_role() == 'superadmin'):

            $user = User::whereEmail(request('email'))->first();
            $randomPassword = Str::random(12);
            
            if($user):
                $user->otherProjects()->attach($pid, ['role' => request('role')]);
                $user->save();
                $user->notify(new AddedToProject($project));
            else:
                $u = new User;
                $u->name = request('name');
                $u->email = request('email');
                $u->password = bcrypt($randomPassword);
                $u->current_project_id = $pid;
                $u->save();
                $u->otherProjects()->attach($pid, ['role' => request('role')]);
                $u->notify(new AddedToProject($project));

                // send welcome e-mail with password
                Mail::to($u->email)->send(new NewAccountCreated($u, $randomPassword, $project));
                
            endif;

            return redirect()->back()->with('status', 'Member added');

        endif;        

        return redirect()->back()->withErrors(['You cannot add a member']);
    }
}