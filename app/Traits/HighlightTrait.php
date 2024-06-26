<?php

namespace App\Traits;
use DB;
use Illuminate\Support\Facades\Log;


trait HighlightTrait {

    public function getHighlightTrait($highlight)
    {
        try{
           $element = DB::table('highlights')->where('highlightId', $highlight['id'])->where('highlightPage', $highlight['page'])->where('highlightTop', $highlight['top'])->where('highlightLeft', $highlight['left'])->where('highlightHeight', $highlight['height'])->where('highlightWidth', $highlight['width'])->where('highlightClassname', $highlight['classname'])->where('highlightText', $highlight['text'])->first();
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'highlight' => $element
        ]);
    }

    public function insertHighlightTrait($highlight)
    {
        try{
           $res = DB::table('highlights')->insert([
                'highlightId' => $highlight['id'],
                'highlightPage' => $highlight['page'],
                'highlightTop' => $highlight['top'],
                'highlightLeft' => $highlight['left'],
                'highlightHeight' => $highlight['height'],
                'highlightWidth' => $highlight['width'],
                'highlightClassname' => $highlight['classname'],
                'highlightText' => $highlight['text'],
                ]); 
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'highlight' => $res
        ]);
    }

    public function getLikesOfUserTrait($userId)
    {
        try{
            $getHighlights = DB::table('user_book_highlight')->where('userId', $userId)->where('isLiked', '1')->pluck('bookId', 'highlightId')->toArray();

        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'highlights' => $getHighlights
        ]);
    }

    public function getBooksOfUser($userId)
    {
         try{
            $getBookIds = DB::table('user_book_highlight')->where('userId', $userId)->where('isLiked', '1')->pluck('bookId')->toArray();

        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'books' => $getBookIds
        ]);
    }

    public function getHighlightsOfUser($userId)
    {
         try{

            $getHighlightIds = DB::table('user_book_highlight')->where('userId', $userId)->where('isLiked', '1')->pluck('highlightId')->toArray();

        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'highlights' => $getHighlightIds
        ]);
    }


}