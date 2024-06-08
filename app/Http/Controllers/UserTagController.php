<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\UserTagTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserTagController extends Controller
{
    //

    use UserTagTrait;


    public function getColorTags(Request $request)
    {
        $user = Auth::user();

        $color = json_decode($this->getColorTagsTrait($user->id));

        if($color->response == "success") return response()->json(['colors'=>$color], 200); 
        return response()->json(['colors'=>$color], 400); 

    }

    public function editColorTags(Request $request)
    {
        $user = Auth::user();
        $updatedColors = $request->all();

        $colors = json_decode($this->editColorTagsTrait($user->id, $updatedColors));

        log::info("colors after edit: " . print_r($colors, true));

        if($colors->response == "success") return response()->json("Colors updated succesfully", 200); 
        return response()->json("Colors not updated", 400); 

    }

}
