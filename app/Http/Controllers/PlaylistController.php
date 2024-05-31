<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

use DB;

use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function getUserPlaylist(Request $request)
    {
        $user = Auth::user();

        $playlists = DB::table('songplaylist')->where('userId', $user->id)->select('playlistId')->distinct()->get();

        $playlistArray = [];
        $playData = null;

        $map = new Collection();

        for($i=0; $i<count($playlists);$i++)
        {
            $songArray = [];

            $playData = DB::table('playlists')->where('playlistId', $playlists[$i]->playlistId)->first();

            array_push($playlistArray, $playData);

            $songsFromPlaylist = DB::table('songplaylist')->where('userId', $user->id)->where('playlistId', $playlists[$i]->playlistId)->select('songId')->distinct()->get();

            for($j=0; $j<count($songsFromPlaylist);$j++)
            {
                $songData = DB::table('songs')->where('songId', $songsFromPlaylist[$j]->songId)->first();

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
}
