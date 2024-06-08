<?php

namespace App\Traits;
use DB;
use Illuminate\Support\Facades\Log;


trait UserBookTrait {
    public function getUserBookTrait($userId, $bookId)
    {
        try{
            $bookInDB = DB::table('userBooks')->where('userId', $userId)->where('bookId', $bookId)->get()->first();
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'book' => $bookInDB
        ]);
    }

    public function insertUserBookTrait($user, $book)
    {
        try{
            $idNewUserBook = DB::table('userBooks')->insertGetId([
                'userId' => $user,
                'bookId' => $book
            ]); 
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'book' => $idNewUserBook
        ]);
    }

}