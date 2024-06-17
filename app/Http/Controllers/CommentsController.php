<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\BookTrait;
use App\Traits\CommentTrait;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    use BookTrait, CommentTrait;
    public function addComment(Request $request)
    {
        $user = Auth::user();
        $comment = $request->comment;
        $bookIdentifier = $request->book;

        $bookResponse = json_decode($this->getBookByIdentifierTrait($bookIdentifier));

        if($bookResponse->response == "success") $bookId = $bookResponse->book->bookId;
        else return response()->json(['message' => 'Book not found'], 500);
         

        $responseInsert = json_decode($this->insertCommentTrait($comment));

        if($responseInsert->response == "success") $commentId = $responseInsert->comment;
        else return response()->json(['message' => 'Error inserting comment'], 500);

        $responseUserInsert = json_decode($this->insertUserComment($commentId, $bookId, $user->id));

        if($responseUserInsert->response == "success") return response()->json(['message' => 'Comment inserted successfully'], 200);
        else return response()->json(['message' => 'Error inserting comment to user'], 500);

    }
}
