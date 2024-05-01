<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function getUserPlaylist(Request $request)
    {
        // Get authenticated user
        $user = Auth::user();

        // Get data associated with the user or whatever logic you need
        $data = Data::where('user_id', $user->id)->get();

        // Return data along with user details
        return response()->json([
            'user' => $user,
            'data' => $data,
        ]);
    }
}
