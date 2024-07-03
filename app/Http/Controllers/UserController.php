<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\UserTrait;


class UserController extends Controller
{
    //

    use UserTrait;

    public function getUsers()
    {
        // $users = DB::table('users')->get();
        // return response()->json($users);

        $users = $this->getUsersTrait();
        return $users;
    }

    public function getCurrentUser(Request $request)
    {
        $user = $request->user();

        return response()->json($user);

    }

    public function userData(Request $request)
    {
        $user = Auth::user();

        return response()->json(['user'=>$user], 200);
    }

    public function otherUserData(Request $request)
    {
        $userId = $request->userId;
        
        $userIdRes = json_decode($this->getUserTrait($userId));

        if($userIdRes->response == "success") return response()->json(['user'=> $userIdRes->user], 200);
        else return response()->json(['message'=> $userIdRes->error], 500);
    }

    public function editUser(Request $request)
    {
        log::info("enetered edit");
        $user = Auth::user();
        $updatedUser = $request->all();

        log::info("update user data: " . print_r($updatedUser, true));

        $edit = json_decode($this->editUserTrait($user->id, $updatedUser));

        return response()->json(['message'=>"User updated succesfully", 'user'=>$edit], 200); 

    }

    public function modifyDate(Request $request)
    {
        $user = Auth::user();
        $date = $request->all();
        log::info("here is date: " . print_r($date, true));
        $edit = json_decode($this->modifyDateTrait($user->id, $date["date"]));

        if($edit->response == "success") return response()->json(['message'=>"Recommandation date updated succesfully", 'user'=>$edit], 200); 

        return response()->json(['message'=>"Recommandation date not updated", 'error'=>$edit->error], 500); 

    }
}
