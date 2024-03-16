<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;


class BookController extends Controller
{
    public function userBook(Request $request)
    {
        $user = $request->userId;
        $bookIdentifier = $request->bookIdentifier;
        $getBook = DB::table('books')->where('bookIdentifier', $bookIdentifier)->first();

        if(isset($getBook->bookId))
        {
            $bookId = $getBook->bookId;
        }
        else{
            return response()->json(['message' => 'Book not found'], 404);
        }

        $getUserBook = DB::table('userBooks')->where('userId', $user)->where('bookId', $bookId)->first();

        if($getUserBook == null)
        {
            return response()->json(['message' => 'User does not have this book in library', 'has'=>false], 200);
        }

        else return response()->json(['message' => 'User has book in library', 'has'=>true], 200); 
    }
}
