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

    public function editUser(Request $request)
    {
        log::info("enetered edit");
        $user = Auth::user();
        $updatedUser = $request->all();

        log::info("update user data: " . print_r($updatedUser, true));

        $edit = json_decode($this->editUserTrait($user->id, $updatedUser));

        return response()->json(['message'=>"User updated succesfully", 'user'=>$edit], 200); 

    }

    public function getColorTags(Request $request)
    {
        $user = Auth::user();

        $color = DB::table('usertags')->where('userId', $user->id)->select('red', 'blue', 'green', 'orange')->first();

        return response()->json(['colors'=>$color], 200); 
    }

    public function editColorTags(Request $request)
    {
        $user = Auth::user();
        $updatedColors = $request->all();

        $updateColors = DB::table('usertags')->where('userId', $user->id)->update(['red' => $updatedColors["red"], 'blue' => $updatedColors["blue"],
        'green' => $updatedColors["green"], 'orange' => $updatedColors["orange"]]);

        return response()->json("Colors updated succesfully", 200); 

    }
}
