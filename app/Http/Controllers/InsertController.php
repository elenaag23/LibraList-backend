<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class InsertController extends Controller
{
    public function insertBook(Request $request)
    {
        log::info("book request: " . print_r($request, true));

        return response()->json();
    }
}
