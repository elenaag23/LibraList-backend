<?php

namespace App\Traits;
use DB;
use Illuminate\Support\Facades\Log;


trait CommentTrait {

    public function insertCommentTrait($comment)
    {
        try{
            $idComment = DB::table('comments')->insertGetId([
                'commentText' => $comment,
                'commentDate' => DB::raw('NOW()'),
                ]);

        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'comment' => $idComment
        ]);
    }

    public function insertUserComment($commentId, $bookId, $userId)
    {
        try{
            $idCommentUser = DB::table('usercomments')->insertGetId([
                'userId' => $userId,
                'bookId' => $bookId,
                'commentId' => $commentId,
                ]);

        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'idCommentUser' => $idCommentUser
        ]);
    }
}
