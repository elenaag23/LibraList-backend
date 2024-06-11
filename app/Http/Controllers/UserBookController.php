<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\UserBookTrait;
use App\Traits\BookTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserBookController extends Controller
{
    //
    use UserBookTrait, BookTrait;

    public function addToLibrary(Request $request)
    {
        log::info("parameters to add to library: " . print_r($request->all(), true));
        $user = Auth::user();
        $bookIdentifier = $request->book;

        log::info("here is the user: " . print_r($user, true));

        //$bookId = self::getBookId($bookIdentifier);

        $bookRes = json_decode($this->getBookByIdentifierTrait($bookIdentifier));

        if($bookRes->book != null) $bookId = $bookRes->book->bookId;
        else return response()->json(["No book found"], 404);

        //$bookInDB = DB::table('userBooks')->where('userId', $userId)->where('bookId', $bookId)->get()->first();

        $userBook = json_decode($this->getUserBookTrait($user->id, $bookId));

        if($userBook->book == null)
        {
            //$res = self::insertBookUserFunction($userId, $bookId);
            $res = json_decode($this->insertUserBookTrait($user->id, $bookId));

            if($res->response == "success") return response()->json(["Book added succesfully"], 200);
            else return response()->json(["Failed to insert"], 400);
            //log::info("res to insert book to user: " . print_r($res->getStatusCode(), true));
        }
    }

    public function insertBookUserFunction(int $user, int $book)
    {
        try {
            $idNewUserBook = DB::table('userBooks')->insertGetId([
                'userId' => $user,
                'bookId' => $book
            ]); 
            return response()->json(['message' => 'User added Book successfully'], 201);
        } catch (\Exception $e) {
            Log::error("Error inserting book to user: " . $e->getMessage());

            return response()->json(['error' => 'Failed to insert book to user'], 500);
        }
    }

}
