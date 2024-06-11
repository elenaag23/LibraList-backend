<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class ExternalController extends Controller
{
    //
    public function fetchPdf($identifier)
    {
        log::info("identifiers: " . print_r($identifier, true));
        $apiUrl = 'https://archive.org/download/'.urlencode($identifier);
        $client = new Client();
        $response = $client->get($apiUrl);
        
        return response($response->getBody(), 200)
            ->header('Content-Type', 'application/pdf');
    }
}
