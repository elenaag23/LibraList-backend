<?php

namespace App\Traits;
use DB;


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
}