<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;


class InsertController extends Controller
{



    public function getSong(String $link)
    {
        $song = DB::table('songs')->where('songLink', $link)->first();

        if($song != null)
        {
            return $song->songId;
        }

        return null;
    }

}
