<?php

namespace App\Traits;
use DB;
use Illuminate\Support\Facades\Log;


trait UserTagTrait {

    public function getColorTagsTrait($userId)
    {
        try{
            $color = DB::table('usertags')->where('userId', $userId)->select('red', 'blue', 'green', 'orange')->first();
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'colors' => $color
        ]);
    }

    public function editColorTagsTrait($userId, $updatedColors)
    {
        try{
            $updateColors = DB::table('usertags')->where('userId', $userId)->update(['red' => $updatedColors["red"], 'blue' => $updatedColors["blue"],
            'green' => $updatedColors["green"], 'orange' => $updatedColors["orange"]]);
           // log::info("retrieved colors: " . print_r($color, true));
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        if(empty($updateColors)) {
            return json_encode([
                'response' => 'failure',
                'error' => 'User tags not modified'
            ]);
        }

        return json_encode([
            'response' => 'success',
            'colors' => $updateColors
        ]);
    }

}