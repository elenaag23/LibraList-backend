<?php

namespace App\Traits;
use DB;
use Illuminate\Support\Facades\Log;


trait UserTrait {

    public function getUsersTrait()
    {
        try{
            $users = DB::table('users')->get();
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        if($users->isEmpty()) {
            return json_encode([
                'response' => 'failure',
                'error' => 'There are no users in the database'
            ]);
        }

        return json_encode([
            'response' => 'success',
            'users' => $users
        ]);
    }

     public function editUserTrait($userId, $userData)
    {
        try{
            if(isset($userData["password"]) && $userData["password"]!=null) $updateUser = DB::table('users')->where('id', $userId)->update(['password' => Hash::make($userData["password"])]);

            $updateUser = DB::table('users')->where('id', $userId)->update(['name' => $userData["name"], 'email' => $userData["email"]]);

            log::info("response: " . print_r($updateUser, true));

        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'edit' => $updateUser
        ]);
    }

    public function getColorTagsTrait($userId)
    {
        try{
            $color = DB::table('usertags')->where('userId', $userId)->select('red', 'blue', 'green', 'orange')->first();

            log::info("retrieved colors: " . print_r($color, true));
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        if(empty($color)) {
            return json_encode([
                'response' => 'failure',
                'error' => 'User has no color tags set'
            ]);
        }

        return json_encode([
            'response' => 'success',
            'colors' => $color
        ]);
    }
}