<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Traits\PlaylistTrait;
use App\Traits\BookTrait;

use DB;

use Illuminate\Http\Request;

class PlaylistController extends Controller

{
    use PlaylistTrait, BookTrait;

    public function getUserPlaylist(Request $request)
    {
        $user = Auth::user();

        $playlistRes = json_decode($this->getUserPlaylistTrait($user->id));

        if($playlistRes->response == "success" && count($playlistRes->playlists) > 0) $playlists = $playlistRes->playlists;
        else return response()->json(["message"=>"User has no playlists"], 400); 

        //$playlists = DB::table('songplaylist')->where('userId', $user->id)->select('playlistId')->distinct()->get();

        $playlistArray = [];
        $playData = null;

        $map = new Collection();

        for($i=0; $i<count($playlists);$i++)
        {
            $songArray = [];

            //$playData = DB::table('playlists')->where('playlistId', $playlists[$i]->playlistId)->first();
            $playDataRes = json_decode($this->getPlaylistByIdTrait($playlists[$i]->playlistId));

            if($playDataRes->response == "success") $playData = $playDataRes->playlists;
            else return response()->json(["message"=>"No playlist with this id"], 400); 

            array_push($playlistArray, $playData);

            //$songsFromPlaylist = DB::table('songplaylist')->where('userId', $user->id)->where('playlistId', $playlists[$i]->playlistId)->select('songId')->distinct()->get();

            $songsData = json_decode($this->getSongsPlaylistTrait($user->id, $playlists[$i]->playlistId));

            if($songsData->response == "success") $songsFromPlaylist = $songsData->songs;
            else return response()->json(["message"=>"No songs in this playlist"], 400); 

            for($j=0; $j<count($songsFromPlaylist);$j++)
            {
                //$songData = DB::table('songs')->where('songId', $songsFromPlaylist[$j]->songId)->first();

                $songDataRes = json_decode($this->getSongTrait($songsFromPlaylist[$j]->songId));

                if($songDataRes->response == "success") $songData = $songDataRes->song;
                else return response()->json(["message"=>"No song with this id"], 400); 

                array_push($songArray, $songData);
            }

            $map->put((int)$playlists[$i]->playlistId, $songArray);
        }

        log::info("playlists data: " . print_r($playData, true));
        log::info("playlists: " . print_r($playlists, true));
        log::info("map: " . print_r($map, true));
        
        // Return data along with user details
        return response()->json([
            'user' => $user,
            'playlists' => $playlists[0]->playlistId,
            'playlistData' => $playlistArray, 
            'map' => $map
        ], 200);
    }

    public function savePlaylist(Request $request)
    {
        $user = Auth::user();
        $bookIdentifier = $request->book;
        $links = $request->links;
        $date = $request->date;
        $playlistName = $request->playlistName;

        //$bookId = self::getBookId($bookIdentifier);

        $bookRes = json_decode($this->getBookByIdentifierTrait($bookIdentifier));

        if($bookRes->response == "success" && $bookRes->book != null) $bookId = $bookRes->book->bookId;
        else return response()->json(["message"=>"No book with this id"], 404);  

        // create playlist

        $playlistRes = json_decode($this->insertPlaylistTrait($playlistName, $date));
        
        if($playlistRes->response == "success" && $playlistRes->playlistId != null ) $playlistId = $playlistRes->playlistId;
        else return response()->json(["message"=>"Error inserting playlist"], 400); 

        foreach($links as $song)
        {

            //verify if song exists

            //$getSong = self::getSong($song["link"]);

            $songRes = json_decode($this->getSongByLinkTrait($song["link"]));

            if($songRes->response == "success" && $songRes->song != null) $songId = $songRes->song->songId; 
            else
            {
                $songId = DB::table('songs')->insertGetId([
                    'songName' => $song["name"],
                    'songArtist' => $song["artist"],
                    'songUrl' => $song["url"],
                    'songLink' => $song["link"],
                    ]); 

                $songInsert = json_decode($this->insertSongTrait($song));

                if($songInsert->response == "success" && $songInsert->song != null) $songId = $songInsert->song;
                else return response()->json(["message"=>"Error inserting song"], 400); 
            }
                // return response()->json(['message' => 'Song inserted successfully'], 201);

        
            $insert = json_decode($this->insertSongPlaylistTrait($user->id, $bookId, $playlistId, $songId));

            if($insert->response != "success") return response()->json(['error' => 'Failed to insert playlist to user'], 500);

        } 

        return response()->json(['message' => 'Playlist inserted successfully'], 201);
    }
}
