<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use App\Mail\NewAccountCreated;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function index(){
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed',
            'project' => 'required',
        ]);
        $data = array();
        if ($validator->fails()) {
            $data['errors'] = $validator->errors();
            return Inertia::render('Auth/Register',$data);
            // ->toResponse($request)->setStatusCode(403)
        }

        // add user
        $getRequest = $request->all();
        $user = new User;
        $user->name = $getRequest['name'];
        $user->email = $getRequest['email'];
        $user->password = bcrypt($getRequest['password']);
        $user->save();

        // add Project
        $project_name = $request->input('name');
        $project = Project::where('name',$project_name)->first();
        if(!$project){
            $project = new Project;
            $project->user_id = $user->id;
            $project->name = $project_name;
            $project->ucode = Str::random(8);
            $project->api_token = Hash::make(Str::random(60));
            $project->save();
        }

        $user->current_project_id = $project->id;
        $user->save();
        
        $projectUser = array();
        $projectUser['project_id'] = $project->id;
        $projectUser['user_id'] = $user->id;
        $projectUser['role'] = 'editor';

        DB::table('project_user')->insert($projectUser);
        
        
        Mail::to($user->email)->send(new NewAccountCreated($user, $getRequest['password'], null));
        $data['success'] = "Please check your email to confirm email address.";
        return redirect()->to('login')->with("status","Please check your email to confirm email address.");
    }

    public function verified($id){
        $decodedId = base64_decode($id);
        $user = User::find($decodedId);
        if($user){
            $user->email_verified_at = Carbon::now();
            $user->save();
            return redirect()->to('login');
        }
        else{
            abort(404);
        }
    }
}
