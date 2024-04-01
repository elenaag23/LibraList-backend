<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HighlightsController extends Controller
{
    //
    public function displayHighlights()
    {
        $userMail = $request->userMail;
        $bookIdentifier = $request->bookIdentifier;
        $getUser = DB::table('users')->where('email', $userMail)->select('userId')->get();
        $getBook = DB::table('books')->where('bookIdentifier', $bookIdentifier)->first();
    }
}
