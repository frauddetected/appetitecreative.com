<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPermission;

class UserPermissionController extends Controller
{
    public function updatePermission(Request $request){
        $data = $request->all();
        $msg = 'Something went wrong!';
        if(isset($data['user_id']) && !empty($data['user_id']) && isset($data['project_id']) && !empty($data['project_id'])){
            if(isset($data['section']) && ($data['section']) > 0){
                UserPermission::where('user_id', $data['user_id'])->where('project_id', $data['project_id'])->delete();
                $getSections = $data['section'];
                foreach($getSections as $key=>$value){
                    $userPermission = new UserPermission;
                    $userPermission->user_id = $data['user_id'];
                    $userPermission->project_id = $data['project_id'];
                    $userPermission->section = $value['name'];
                    $userPermission->route = $value['code'];
                    $userPermission->save();
                }
                $msg = 'User Permission Successfully Updated!';
            }
        }
        return redirect()->back()->with('status', $msg);
    }
}
