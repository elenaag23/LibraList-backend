<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;


class InsertController extends Controller
{
    public function insertBook(Request $request)
    {
        log::info("book request: " . print_r($request->all(), true));

        $receivedBooks = $request->all();

        for($i=0 ; $i<count($receivedBooks); $i++)
        {
            $identifier = $receivedBooks[$i]['identifier'];
            log::info("here is the book identifier: " . $identifier);

            $bookInDB = DB::table('books')->where('bookIdentifier', $identifier)->get()->first();

           // log::info("result of db: " . print_r($bookInDB, true));
            log::info("result of db: " . gettype($bookInDB));

            if($bookInDB == null)
            {
                $res = self::insertBookFunction($receivedBooks[$i]);
                log::info("res to insert: " . print_r($res->getStatusCode(), true));
            }
        }
        return response()->json(['message' => 'Books added successfully'], 201)
    ->header('Access-Control-Allow-Origin', '*'); 
 
    }

    public function insertBookFunction(Array $book)
    {
        try {
            $idNewBook = DB::table('books')->insertGetId([
                'bookName' => $book['title'],
                'bookIdentifier' => $book['identifier'],
                'bookUrl' => $book['url'],
                'bookCover' => $book['jpg']
                ]); 
        
                return response()->json(['message' => 'Book inserted successfully'], 201);
        } catch (\Exception $e) {
            Log::error("Error inserting book: " . $e->getMessage());

            return response()->json(['error' => 'Failed to insert book'], 500);
        }
    }

    public function addToLibrary(Request $request)
    {
        log::info("parameters to add to library: " . print_r($request->all(), true));
        $userMail = $request->user;
        $bookIdentifier = $request->book;

        $userId = self::getUser($userMail);
        $bookId = self::getBookId($bookIdentifier);

        $bookInDB = DB::table('userBooks')->where('userId', $userId)->where('bookId', $bookId)->get()->first();

        if($bookInDB == null)
        {
            $res = self::insertBookUserFunction($userId, $bookId);
            log::info("res to insert book to user: " . print_r($res->getStatusCode(), true));
        }
    }

    public function insertBookUserFunction(int $user, int $book)
    {
        try {
            $idNewUserBook = DB::table('userBooks')->insertGetId([
                'userId' => $user,
                'bookId' => $book
                ]); return response()->json(['message' => 'User added Book successfully'], 201);
        } catch (\Exception $e) {
            Log::error("Error inserting book to user: " . $e->getMessage());

            return response()->json(['error' => 'Failed to insert book to user'], 500);
        }
    }

    public function getBookId(String $identifier)
    {
        log::info("reached get book id function: " . gettype($identifier));

        $bookId = DB::table('books')->where('bookIdentifier', $identifier)->first();

        log::info("found book id: " . $bookId->bookId);

        return $bookId->bookId;

    }

    public function getUser($userMail)
    {
        $user = DB::table('users')->where('email', $userMail)->first();

        return $user->id;
    }

    public function addHighlight(Request $request)
    {
        log::info("parameters to add to highlights: " . print_r($request->all(), true));
        $userMail = $request->user;
        $bookIdentifier = $request->book;
        $highlight = $request->highlight;

        $userId = self::getUser($userMail);
        $bookId = self::getBookId($bookIdentifier);

        $insertHigh = self::insertHighlight($highlight);

        if($insertHigh->getStatusCode() == 201)
        {
try {
            $user_highlight_book = DB::table('user_book_highlight')->insertGetId([
                'userId' => $userId,
                'bookId' => $bookId,
                'highlightId' => $highlight['id'],
                ]); return response()->json(['message' => 'User added Book highlight successfully'], 201);
        } catch (\Exception $e) {
            Log::error("Error inserting highlight book to user: " . $e->getMessage());

            return response()->json(['error' => 'Failed to insert highlight book to user'], 500);
        }
        }
    }

    public function insertHighlight($highlight)
    {
        log::info("highlight id: " . $highlight['id']);

        $element = DB::table('highlights')->where('highlightId', $highlight['id'])->where('highlightPage', $highlight['page'])->where('highlightTop', $highlight['top'])->where('highlightLeft', $highlight['left'])->where('highlightHeight', $highlight['height'])->where('highlightWidth', $highlight['width'])->where('highlightClassname', $highlight['classname'])->where('highlightText', $highlight['text'])->first();

        if($element == null)
        {
            try {
            DB::table('highlights')->insert([
                'highlightId' => $highlight['id'],
                'highlightPage' => $highlight['page'],
                'highlightTop' => $highlight['top'],
                'highlightLeft' => $highlight['left'],
                'highlightHeight' => $highlight['height'],
                'highlightWidth' => $highlight['width'],
                'highlightClassname' => $highlight['classname'],
                'highlightText' => $highlight['text'],
                ]); 
        
                return response()->json(['message' => 'Highlight inserted successfully'], 201);
        } catch (\Exception $e) {
            Log::error("Error inserting highlight: " . $e->getMessage());

            return response()->json(['error' => 'Failed to insert highlight'], 500);
        }
        }
    }

}
