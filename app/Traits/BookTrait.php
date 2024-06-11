<?php

namespace App\Traits;
use DB;
use Illuminate\Support\Facades\Log;


trait BookTrait {

    public function getBookByIdentifierTrait($identifier)
    {
        try{
            $bookInDB = DB::table('books')->where('bookIdentifier', $identifier)->get()->first();
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

    public function insertBookTrait($book)
    {
        try{
            $idNewBook = DB::table('books')->insertGetId([
                'bookName' => $book['title'],
                'bookIdentifier' => $book['identifier'],
                'bookUrl' => $book['url'],
                'bookCover' => $book['jpg'],
                'bookPages' => 1
                ]);

        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'book' => $idNewBook
        ]);
    }

}