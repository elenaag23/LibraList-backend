<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

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

    public function booksHighlights(Request $request)
    {
        $user = Auth::user();

        $map = DB::table('user_book_highlight')->where('userId', $user->id)->pluck('bookId', 'highlightId')->toArray();


        // $getData = DB::table('user_book_highlight')->where('userId', $user->id)->pluck('bookId', 'highlightId')->toArray();

        $getBooks = DB::table('user_book_highlight')->where('userId', $user->id)->pluck('bookId');

        $getHighlights = DB::table('user_book_highlight')->where('userId', $user->id)->pluck('highlightId');

        $getBookData = DB::table('books')->whereIn('bookId', $getBooks)->get()->toArray();

        $getHighlightData = DB::table('highlights')->whereIn('highlightId', $getHighlights)->get()->toArray();

        $getLikes = DB::table('user_book_highlight')->where('isLiked', 1)->pluck('highlightId')->toArray();

        $results = new Collection();
        $colorMap = new Collection();
        
        for($i = 0 ; $i<count($getBooks); $i++)
        {
            $bookHighlightIds = DB::table('user_book_highlight')->where('userId', $user->id)->where('bookId', $getBooks[$i])->pluck('highlightId');

            // $getHighlights = DB::table('highlights')->whereIn('highlightId', $bookHighlightIds)->get()->toArray();

            $getHighlightColors = DB::table('highlights')->whereIn('highlightId', $bookHighlightIds)->distinct()->pluck('highlightClassname');

            $highlights = new Collection();
            

            for($j = 0 ; $j < count($getHighlightColors) ; $j++)
            {
                $getHighlights = DB::table('highlights')->whereIn('highlightId', $bookHighlightIds)->where('highlightClassname', $getHighlightColors[$j])->get()->toArray();

                $highlights->put($getHighlightColors[$j], $getHighlights);
            }

            $results->put((int)$getBooks[$i], $highlights);
            $colorMap->put((int)$getBooks[$i], $getHighlightColors);
        }

        return response()->json(['map' => $results, 'books'=>$getBookData, 'colors'=>$colorMap, 'liked' => $getLikes], 200);
    }

    public function toggleLike(Request $request)
    {
        $req = $request->all();

        $highlightId =$req[0];

        $user = Auth::user();

        $getLike = DB::table('user_book_highlight')->where('userId', $user->id)->where('highlightId', $highlightId)->select('isLiked')->first();

        $updateLike = DB::table('user_book_highlight')->where('userId', $user->id)->where('highlightId', $highlightId)->update(['isLiked' => !$getLike->isLiked]);

        if($updateLike == 1)
        return response()->json(['highlightId'=>$req, 'getLike' => $getLike->isLiked, 'update'=>$updateLike], 200);

        else
        {
            return response()->json(['update'=>$updateLike], 500);
        }
    }

    public function getLikes(Request $request)
    {
        $user = Auth::user();

        $getHighlights = DB::table('user_book_highlight')->where('userId', $user->id)->where('isLiked', '1')->pluck('bookId', 'highlightId')->toArray();

        $getBookIds = DB::table('user_book_highlight')->where('userId', $user->id)->where('isLiked', '1')->pluck('bookId')->toArray();

        $getHighlightIds = DB::table('user_book_highlight')->where('userId', $user->id)->where('isLiked', '1')->pluck('highlightId')->toArray();

        $books = DB::table('books')->whereIn('bookId', $getBookIds)->pluck('bookName', 'bookId')->toArray();

        $highlights = DB::table('highlights')->whereIn('highlightId', $getHighlightIds)->pluck('highlightText', 'highlightId')->toArray();

        return response()->json(['books'=>$books, 'map'=>$getHighlights, 'highlights'=>$highlights], 200);

    }
}
