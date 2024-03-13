<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    //
    public function getUsers()
    {
        $users = DB::table('users')->get();
        return response()->json($users);
    }

        public function fetchPdf($identifier)
    {
        log::info("identifiers: " . print_r($identifier, true));
        $apiUrl = 'https://archive.org/download/'.urlencode($identifier);
        $client = new Client();
        $response = $client->get($apiUrl);
        
        return response($response->getBody(), 200)
            ->header('Content-Type', 'application/pdf');
    }

    public function getCurrentUser(Request $request)
    {
        $user = $request->user();

        return response()->json($user);

    }
}
