<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;



class BookController extends Controller
{
    public function userBook(Request $request)
    {
        $userMail = $request->userMail;
        $bookIdentifier = $request->bookIdentifier;
        $getUser = self::getUser($userMail);
        $getBook = DB::table('books')->where('bookIdentifier', $bookIdentifier)->first();

        if(isset($getBook->bookId))
        {
            $bookId = $getBook->bookId;
        }
        else{
            return response()->json(['message' => 'Book not found'], 404);
        }

        $getUserBook = DB::table('userBooks')->where('userId', $getUser)->where('bookId', $bookId)->first();

        if($getUserBook == null)
        {
            return response()->json(['message' => 'User does not have this book in library', 'has'=>false], 200);
        }

        else return response()->json(['message' => 'User has book in library', 'has'=>true], 200); 
    }

        public function getBookList(Request $request)
    {
        $userMail = $request->userMail;

        $user = self::getUser($userMail);

        $getUserBooks = DB::table('userBooks')->where('userId', $user)->get()->toArray();

        log::info('user has following books: ' . print_r($getUserBooks, true));

        if($getUserBooks == null)
        {
            return response()->json(['message' => 'User does not have books in library', 'books'=>[]], 200);
        }

        else{
            $bookArray = [];
            for($i=0 ; $i<count($getUserBooks) ; $i++)
            {
                $getBook = self::getBook($getUserBooks[$i]->bookId);
                array_push($bookArray, $getBook->getContent());
            }

            log::info("user has following books: " . print_r($bookArray, true));

            return response()->json(['message' => 'User books', 'books'=>$bookArray], 200);
        }
    }

    public function getBook($bookId)
    {
        $book = DB::table('books')->where('bookId', $bookId)->first();

        $foundBook = [
            'title' => $book->bookName,
            'identifier' => $book->bookIdentifier,
            'jpg' => $book->bookCover,
            'url' => $book->bookUrl,
        ];

        return response()->json($foundBook);
    }

        public function getUser($userMail)
    {
        $user = DB::table('users')->where('email', $userMail)->first();

        return $user->id;
    }

    public function getPdf(Request $request)
    {
         try {
            $pdfUrl = $request->input('url');
            $response = Http::get($pdfUrl);

            log::info("responseeee: " . print_r($response, true));
            return response($response->body())->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            log::info("exception: " . print_r($e, true));
            return response('Error fetching PDF', 500);
        }
    }
}
