<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InsertController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\HighlightsController;
use App\Http\Controllers\UserTagController;
use App\Http\Controllers\UserBookController;
use App\Http\Controllers\CommentsController;

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
    Route::put('/editUser', [UserController::class, 'editUser']);
    Route::put('/editColorTags', [UserTagController::class, 'editColorTags']);
    Route::get('/getColorTags', [UserTagController::class, 'getColorTags']);
    Route::get('/userPlaylists', [PlaylistController::class, 'getUserPlaylist']);
    Route::get('/getBookRecommendations', [BookController::class, 'getBookRecommendations']);
    Route::get('/userData', [UserController::class, 'userData']);
    Route::get('/booksHighlights', [HighlightsController::class, 'booksHighlights']);
    Route::put('/toggleLike', [HighlightsController::class, 'toggleLike']);
    Route::get('/getLikes', [HighlightsController::class, 'getLikes']);
    Route::get('/getRecommendations', [BookController::class, 'getRecommendations']);
    Route::put('/editRating', [BookController::class, 'editRating']);
    Route::get('/getFavBooks', [BookController::class, 'getFavBooks']);
    Route::post('/addToLibrary', [UserBookController::class, 'addToLibrary']);
    Route::post('/addHighlight', [HighlightsController::class, 'addHighlight']);
    Route::post('/savePlaylist', [PlaylistController::class, 'savePlaylist']);
    Route::post('/addComment', [CommentsController::class, 'addComment']);

});

Route::get('/getFavBooksUser', [BookController::class, 'getFavBooksUser']);
Route::get('/getLikesUser', [HighlightsController::class, 'getLikesOfUser']);
Route::get('/otherUserData', [UserController::class, 'otherUserData']);
Route::get('/getColorTagsUser', [UserTagController::class, 'getColorTagsUser']);
Route::get('/getComments', [CommentsController::class, 'getComments']);
Route::put('/editComment', [CommentsController::class, 'editComment']);
Route::delete('/deleteComment', [CommentsController::class, 'deleteComment']);
Route::get('/getUsers', [UserController::class, 'getUsers']);
Route::post('/login', [RegisterController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/insertBook', [BookController::class, 'insertBook']);
Route::get('/userBook', [BookController::class, 'userBook']);
Route::get('/bookList', [BookController::class, 'getBookList']);
Route::get('/getpdf', [BookController::class, 'getPdf']);
Route::get('/userHighlightsBook', [BookController::class, 'userHighlightsBook']);
Route::post('/displayHighlights', [HighlightsController::class, 'displayHighlights']);
Route::patch('/setPage', [BookController::class, 'setPage']);
Route::delete('/deleteBook', [BookController::class, 'deleteBook']);
Route::get('/getBookInfo', [BookController::class, 'getBookInfo']);
Route::get('/getBookData', [BookController::class, 'getBookData']);






// get('/user', function (Request $request) {
//     return $request->user();
// });


