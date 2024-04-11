<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InsertController;
use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function (){
    Route::get('/authUser', [UserController::class, 'getCurrentUser']);

});

Route::get('/getUsers', [UserController::class, 'getUsers']);
Route::post('/login', [RegisterController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/insertBook', [InsertController::class, 'insertBook']);
Route::post('/addToLibrary', [InsertController::class, 'addToLibrary']);
Route::get('/userBook', [BookController::class, 'userBook']);
Route::get('/bookList', [BookController::class, 'getBookList']);
Route::get('/getpdf', [BookController::class, 'getPdf']);
Route::get('/userHighlightsBook', [BookController::class, 'userHighlightsBook']);
Route::post('/addHighlight', [InsertController::class, 'addHighlight']);
Route::post('/displayHighlights', [HighlightsController::class, 'displayHighlights']);
Route::patch('/setPage', [BookController::class, 'setPage']);






// get('/user', function (Request $request) {
//     return $request->user();
// });


