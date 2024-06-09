<?php

namespace App\Traits;
use DB;
use Illuminate\Support\Facades\Log;

trait PlaylistTrait {

    public function getPlaylistByIdTrait($playlistId)
    {
        try{
            $playData = DB::table('playlists')->where('playlistId', $playlistId)->first();
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        if(empty($playData)) {
            return json_encode([
                'response' => 'failure',
                'error' => 'There are no playlists with this id'
            ]);
        }

        return json_encode([
            'response' => 'success',
            'playlists' => $playData
        ]);
    }

    public function getUserPlaylistTrait($userId)
    {
        try{
            $playlists = DB::table('songplaylist')->where('userId', $userId)->select('playlistId')->distinct()->get();
        }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        if(empty($playlists)) {
            return json_encode([
                'response' => 'failure',
                'error' => 'There are no playlists of this user'
            ]);
        }

        return json_encode([
            'response' => 'success',
            'playlists' => $playlists
        ]);
    }

    public function getSongsPlaylistTrait($userId, $playlistId)
    {
        try{
            $songsFromPlaylist = DB::table('songplaylist')->where('userId', $userId)->where('playlistId', $playlistId)->select('songId')->distinct()->get();
             }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        if(empty($songsFromPlaylist)) {
            return json_encode([
                'response' => 'failure',
                'error' => 'There are no songs in this playlist'
            ]);
        }

        return json_encode([
            'response' => 'success',
            'songs' => $songsFromPlaylist
        ]);

    }

    public function getSongTrait($songId)
    {
        try{
            $songData = DB::table('songs')->where('songId', $songId)->first();
            }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        if(empty($songData)) {
            return json_encode([
                'response' => 'failure',
                'error' => 'There is no song with this id'
            ]);
        }

        return json_encode([
            'response' => 'success',
            'song' => $songData
        ]);
    }

    public function insertPlaylistTrait($playlistName, $date)
    {
        try{
             $playlistId = DB::table('playlists')->insertGetId([
               'playlistName' => $playlistName,
               'playlistDate' => $date,
                ]); 
            }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'playlistId' => $playlistId
        ]);
    }

    public function getSongByLinkTrait($songLink)
    {
        try{
             $song = DB::table('songs')->where('songLink', $link)->first();
            }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        if(empty($song)) {
            return json_encode([
                'response' => 'failure',
                'error' => 'There is no song with this id'
            ]);
        }

        return json_encode([
            'response' => 'success',
            'song' => $song
        ]);
    }

    public function insertSongTrait($song)
    {
        try{
            $songId = DB::table('songs')->insertGetId([
                'songName' => $song["name"],
                'songArtist' => $song["artist"],
                'songUrl' => $song["url"],
                'songLink' => $song["link"],
                ]); 
            }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'song' => $songId
        ]);
    }

    public function insertSongPlaylistTrait($userId, $bookId, $playlistId, $songId)
    {
        try{
            $res = DB::table('songPlaylist')->insert([
                        'userId' => $userId,
                        'bookId' => $bookId,
                        'playlistId' => $playlistId,
                        'songId' => $songId,
                        ]);
            }catch(\Exception $e){
            return json_encode([
                'response' => 'failed',
                'error' => $e->getMessage()
            ]);
        }

        return json_encode([
            'response' => 'success',
            'value' => $res
        ]);
    }

}