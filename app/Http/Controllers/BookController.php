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
            return response()->json(['message' => 'Book not found', 'has'=>false], 200);
        }

        $getUserBook = DB::table('userBooks')->where('userId', $getUser)->where('bookId', $bookId)->first();

        if($getUserBook == null)
        {
            return response()->json(['message' => 'User does not have this book in library', 'has'=>false], 200);
        }

        else return response()->json(['message' => 'User has book in library', 'has'=>true, 'pageNumber' => $getUserBook->pageNumber], 200); 
    }

    public function getBookList(Request $request)
    {
        $userMail = $request->userMail;

        $user = self::getUser($userMail);

        $getUserBooks = DB::table('userBooks')->where('userId', $user)->orderBy('accessDate', 'desc')->get()->toArray();

        log::info('user has following books: ' . print_r($getUserBooks, true));

        if($getUserBooks == null)
        {
            return response()->json(['message' => 'User does not have books in library', 'books'=>[]], 200);
        }

        else{
            $bookArray = [];
            for($i=0 ; $i<count($getUserBooks) ; $i++)
            {
                $getBook = self::getBook($getUserBooks[$i]->bookId, $getUserBooks[$i]->pageNumber);
                array_push($bookArray, $getBook->getContent());
            }

            log::info("user has following books: " . print_r($bookArray, true));

            return response()->json(['message' => 'User books', 'books'=>$bookArray], 200);
        }
    }

    public function getBook($bookId, $pageNumber)
    {
        $book = DB::table('books')->where('bookId', $bookId)->first();

        $foundBook = [
            'title' => $book->bookName,
            'identifier' => $book->bookIdentifier,
            'jpg' => $book->bookCover,
            'url' => $book->bookUrl,
            'pageNumber' => $pageNumber,
            'totalPages' => $book->bookPages
        ];

        return response()->json($foundBook);
    }

    public function getHighlight($highlightId)
    {
        $highlight = DB::table('highlights')->where('highlightId', $highlightId)->first();

        $found = [
            'id' => $highlight->highlightId,
            'page' => $highlight->highlightPage,
            'top' => $highlight->highlightTop,
            'left' => $highlight->highlightLeft,
            'height' => $highlight->highlightHeight,
            'width' => $highlight->highlightWidth,
            'classname' => $highlight->highlightClassname,
            'text' => $highlight->highlightText,
        ];

        return response()->json($found);
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
            //log::info("exception: " . print_r($e, true));
            return response('Error fetching PDF', 500);
        }
    }

    public function getBookByIdentifier($bookIdentifier)
    {
        $book = DB::table('books')->where('bookIdentifier', $bookIdentifier)->first();

        if ($book != null ) return $book->bookId;
        else return null;
    }

    public function userHighlightsBook(Request $request)
    {
        $userMail = $request->userMail;
        $bookIdentifier = $request->bookIdentifier;

        $userId = self::getUser($userMail);
        $bookId = self::getBookByIdentifier($bookIdentifier);

        if($bookId == null)
        {
            return response()->json(['message' => 'User does not have highlights in library', 'highlights'=>[]], 200);
        }

        $getUserHighlightBooks = DB::table('user_book_highlight')->where('userId', $userId)->where('bookId', $bookId)->get()->toArray();

        log::info('user has following highlights: ' . print_r($getUserHighlightBooks, true));

        if($getUserHighlightBooks == null)
        {
            return response()->json(['message' => 'User does not have highlights in library', 'highlights'=>[]], 200);
        }

        else{
            $highlightsArray = [];
            $colors = [];
            for($i=0 ; $i<count($getUserHighlightBooks) ; $i++)
            {
                $getHighlight = self::getHighlight($getUserHighlightBooks[$i]->highlightId);

                $content = json_decode($getHighlight->getContent());

                if(isset($highlightsArray[(int)$content->page]))
                {
                    array_push($highlightsArray[(int)$content->page], $content);
                }
                else $highlightsArray[(int)$content->page] = [$content];

                if(isset($colors[$content->classname]))
                {
                    array_push($colors[$content->classname], $content);
                }
                else $colors[$content->classname] = [$content];

                //array_push($colors, $content->classname);
            }

            //$colors = collect($colors)->unique();

            log::info("user has following highlights in book: " . print_r($highlightsArray, true));

            return response()->json(['message' => 'User highlights', 'highlights'=>$highlightsArray, 'colors'=>$colors], 200);
        }
    }

    public function setPage(Request $request)
    {
        $userMail = $request->user;
        $book = $request->book;
        $pageNumber = $request->pageNumber;
        $accessDate = $request->accessTime;
        $pagesNumber = $request->bookPages;

        $userId = self::getUser($userMail);
        $bookId = self::getBookByIdentifier($book);

        $updatePagesNumber = DB::table('books')->where('bookId', $bookId)->update(['bookPages' => $pagesNumber]);

        $getBook = DB::table('userbooks')->where('userId', $userId)->where('bookId', $bookId)->update(['pageNumber' => $pageNumber, 'accessDate' => $accessDate]);

        if (DB::table('userbooks')->where('userId', $userId)->where('bookId', $bookId)->where('pageNumber', $pageNumber)->where('accessDate', $accessDate)->exists()) {
            return response()->json(['message' => 'User book page updated successfully'], 200);
        } else {
            return response()->json(['error' => 'User book not found'], 404);
        }
    }

    public function deleteBook(Request $request)
    {
        $userMail = $request->user;
        $bookIdentifier = $request->book;
        $getUser = self::getUser($userMail);
        $getBook = DB::table('books')->where('bookIdentifier', $bookIdentifier)->first();

        if(isset($getBook->bookId))
        {
            $bookId = $getBook->bookId;
        }
        else{
            return response()->json(['message' => 'Book not found'], 404);
        }
        
        $deleteUserBook = DB::table('userBooks')->where('userId', $getUser)->where('bookId', $bookId)->delete();

        log::info("deleted: " . print_r($deleteUserBook, true));


        $getUserHighlightBooks = DB::table('user_book_highlight')->where('userId', $getUser)->where('bookId', $bookId)->pluck('highlightId');

        DB::table('user_book_highlight')->where('userId', $getUser)->where('bookId', $bookId)->delete();

        DB::table('highlights')->whereIn('highlightId', $getUserHighlightBooks)->delete();

        if ($deleteUserBook) {
            // Deletion successful
            return response()->json(['message' => 'The user book record has been successfully deleted.']);
        } else {
            // Deletion failed
            return response()->json(['message' => 'Failed to delete the user book record.'], 500);
        }
    }

}
