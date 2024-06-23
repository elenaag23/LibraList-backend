<?php

namespace App\Traits;
use DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


trait CommentTrait {

    public function insertCommentTrait($comment)
    {
        $currentTimestamp = Carbon::now();

        log::info("date 1: " . print_r($currentTimestamp, true));
        log::info("date 2: " . print_r(DB::raw('NOW()'), true));
        try{
            $idComment = DB::table('comments')->insertGetId([
                'commentText' => $comment,
                'commentDate' => $currentTimestamp,
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

     public function getCommentsTrait($bookId)
    {
        try{
            $commentIds = DB::table('usercomments')->where('bookId', $bookId)->pluck('commentId');
            $userIds = DB::table('usercomments')->where('bookId', $bookId)->pluck('userId');
            $map = DB::table('usercomments')->where('bookId', $bookId)->pluck('userId', 'commentId');
            $comments = DB::table('comments')->whereIn('commentId', $commentIds)->orderBy('commentDate', 'desc')->get()->toArray();
            $users = DB::table('users')->whereIn('id', $userIds)->pluck('name', 'id');
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'comments' => $comments,
            'map' => $map,
            'users' => $users,
        ]);
    }

    public function editCommentTrait($commentId, $commentText)
    {
        try{
            $updateComment = DB::table('comments')->where('commentId', $commentId)->update(['commentText' => $commentText]);
           // log::info("retrieved colors: " . print_r($color, true));
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        if(empty($updateComment)) {
            return json_encode([
                'response' => 'failure',
                'error' => 'User comment not modified'
            ]);
        }

        return json_encode([
            'response' => 'success',
            'updateComment' => $updateComment
        ]);
    }


}
