<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;



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

    public function getBookInfo(Request $request)
    {
        $bookTitle = $request->bookTitle;
        $bookIdentifier = $request->bookIdentifier;
        $apiKey = 'AIzaSyCujE8VRyac9339XeuTFOyIuIovhTb_E-U';
        $url = 'https://www.googleapis.com/books/v1/volumes';

        $bookData = self::getBookDataByIdentifier($bookIdentifier);
        if(!isset($bookData->bookGenre) || !isset($bookData->bookDescription) || $bookData->bookGenre == null || $bookData->bookDescription == null)
        {
            $response = Http::get($url, [
                    'q' => $bookTitle,
                    'key' => $apiKey,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $items = $data["items"];

                for($i = 0; $i<count($items); $i++)
                {
                    log::info("item data: " . print_r($items[$i]["volumeInfo"], true));
                    if(isset($items[$i]["volumeInfo"]["title"]) && isset($items[$i]["volumeInfo"]["authors"]) && isset($items[$i]["volumeInfo"]["description"]) && isset($items[$i]["volumeInfo"]["categories"]) && isset($items[$i]["volumeInfo"]["language"]) && $items[$i]["volumeInfo"]["language"] == "en")
                    {
                        self::insertGenre($bookIdentifier, $items[$i]["volumeInfo"]["description"], $items[$i]["volumeInfo"]["categories"], $items[$i]["volumeInfo"]["title"]);

                        return response()->json(['message' => 'Get book info successfully.', 'bookGenre'=>$items[$i]["volumeInfo"]["categories"], 'bookDescription'=>$items[$i]["volumeInfo"]["description"]], 200);
                        break;
                    }
                }

                return response()->json(['message' => 'Get book info not successful'], 204);
            }
            else{
                return response()->json(['message' => 'Api call not successful'], $response->status());
            }
    } else {
        return response()->json([
            'bookGenre' => $bookData->bookGenre,
             'bookDescription' => $bookData->bookDescription,
            'message' => 'Data found in DB'
            ], 200);
        }
    }

    public function getBookDataByIdentifier($identifier)
    {
        $book = DB::table("books")->where('bookIdentifier', $identifier)->first();

        return $book;
    }

    public function getBookData(Request $request)
    {
        $bookIdentifier = $request->book;

        $bookData = self::getBookDataByIdentifier($bookIdentifier);

        if ($bookData == null)
        {
            return response()->json([
                        'message' => 'No book with given identifier found'
                    ], 204);
        }

        else{
            $book = ['identifier' => $bookData->bookIdentifier,
                    'title' => $bookData->bookName,
                    'jpg' => $bookData->bookCover,
                    'url' => $bookData->bookUrl];
            return response()->json([
                        'book' => $book,
                        'message' => 'Found book in DB'
                    ], 200);
        }
    }

    public function insertGenre($identifier, $description, $category, $title)
    {
        log::info("entered insert genre: " . $identifier);
        $bookData = self::getBookDataByIdentifier($identifier);
        log::info("book data: " . print_r($bookData, true));

        if($bookData != null && (!isset($bookData->bookGenre) || !isset($bookData->bookDescription)))
        {
            log::info("entered if");
            DB::table('books')->where('bookId', $bookData->bookId)->update(['bookGenre' => $category[0], 'bookDescription' => $description, 'bookTitle' => $title]);

            $bookData = self::getBookDataByIdentifier($identifier);
            log::info("data after insert: " . print_r($bookData, true));
        }
    }

    public function getBookRecommendations(Request $request)
    {
        log::info("entered book recommendations");
        $user = Auth::user();

        $userBooks = DB::table('userbooks')->where('userId', $user->id)->get()->toArray();
        $apiKey = 'AIzaSyCujE8VRyac9339XeuTFOyIuIovhTb_E-U';
        $url = 'https://www.googleapis.com/books/v1/volumes';

        $genres = [];
        $titles = [];
        $bookIds = [];
        $bookResults = [];

        for($i = 0; $i<count($userBooks); $i++)
        {
            $book = DB::table('books')->where('bookId', $userBooks[$i]->bookId)->first();

            if($book != null)
            {
                array_push($genres, $book->bookGenre);
                array_push($bookIds, $book->bookId);
            }
        }

        log::info("list of genres: " . print_r($genres, true));

        for($i = 0 ; $i<count($genres); $i++)
        {
            log::info("entered genre for: " . $genres[$i]);
            $booksByGenre = DB::table('books')->where('bookGenre', $genres[$i])->whereNotIn('bookId', $bookIds)->get()->toArray();

            log::info("results: " . print_r($booksByGenre, true));

            if(count($booksByGenre) > 0)
            {
                log::info("entered merge");
                $bookResults = array_merge($bookResults, $booksByGenre);
            }
        }

        // for($i = 0 ; $i<count($genres); $i++)
        // {
        //     log::info("entered for");
        //     $response = Http::get($url, [
        //         'q' => "subject:" . $genres[$i],
        //         'key' => $apiKey,
        //     ]);

        //     if ($response->successful()) {
        //         $data = $response->json();
        //         $items = $data["items"]; 
                

        //         log::info("items to recommend: genre: " . $genres[$i] . " " . print_r($items, true));

        //         for($j = 0 ; $j < count($items); $j++)
        //         {
        //             array_push($titles, $items[$j]["volumeInfo"]["title"]);
        //         }
        //     }
        //     else{
        //         log::info("response not successful: " . $genres[$i]);
        //     }
        // }

        $outputData = [];

        for($i = 0 ; $i < count($bookResults); $i++)
        {
            $object = [
                'identifier' => $bookResults[$i]->bookIdentifier,
                'jpg' => $bookResults[$i]->bookCover,
                'url' => $bookResults[$i]->bookUrl,
                'pagesNumber' => $bookResults[$i]->bookPages,
                'title' => $bookResults[$i]->bookName,
            ];

            array_push($outputData, $object);
        }

        return response()->json(['genreBooks' => $outputData], 200);
    }

}
