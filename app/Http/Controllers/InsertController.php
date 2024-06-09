<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;


class InsertController extends Controller
{

    public function savePlaylist(Request $request)
    {
        $userMail = $request->user;
        $bookIdentifier = $request->book;
        $links = $request->links;
        $date = $request->date;
        $playlistName = $request->playlistName;

        $userId = self::getUser($userMail);
        $bookId = self::getBookId($bookIdentifier);


        // create playlist

        try {
            $playlistId = DB::table('playlists')->insertGetId([
               'playlistName' => $playlistName,
               'playlistDate' => $date,
                ]); 
        
                // return response()->json(['message' => 'Playlist inserted successfully'], 201);
        } catch (\Exception $e) {
            Log::error("Error inserting playlist: " . $e->getMessage());

            return response()->json(['error' => 'Failed to insert playlist'], 500);
        }

        foreach($links as $song)
        {

            //verify if song exists

            $getSong = self::getSong($song["link"]);

            log::info("song: " . print_r($song, true));
            try{

            if($getSong != null) $songId = $getSong;
            else
            {
                $songId = DB::table('songs')->insertGetId([
                    'songName' => $song["name"],
                    'songArtist' => $song["artist"],
                    'songUrl' => $song["url"],
                    'songLink' => $song["link"],
                    ]); 
            }
                // return response()->json(['message' => 'Song inserted successfully'], 201);

                try{
                    DB::table('songPlaylist')->insert([
                        'userId' => $userId,
                        'bookId' => $bookId,
                        'playlistId' => $playlistId,
                        'songId' => $songId,
                        ]);

            }catch(\Exception $e){
                Log::error("Error inserting dependency: " . $e->getMessage());

                return response()->json(['error' => 'Failed to insert song'], 500);
        }

            }catch(\Exception $e){
            Log::error("Error inserting song: " . $e->getMessage());

            return response()->json(['error' => 'Failed to insert song'], 500);
        }
        }

        return response()->json(['message' => 'Playlist inserted successfully'], 201);
    }

    public function getSong(String $link)
    {
        $song = DB::table('songs')->where('songLink', $link)->first();

        if($song != null)
        {
            return $song->songId;
        }

        return null;
    }

}
