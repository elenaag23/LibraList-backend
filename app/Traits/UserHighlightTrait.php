<?php

namespace App\Traits;
use DB;
use Illuminate\Support\Facades\Log;


trait UserHighlightTrait {

    public function insertUserHighlightTrait($userId, $highlightId, $bookId)
    {
        try{
            $user_highlight_book = DB::table('user_book_highlight')->insertGetId([
                'userId' => $userId,
                'bookId' => $bookId,
                'highlightId' => $highlightId,
                ]); 
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'highlight' => $user_highlight_book
        ]);
    }

}