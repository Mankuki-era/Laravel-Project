<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/posts/{post}/like', 'LikesController@like');
Route::post('/posts/{post}/unlike', 'LikesController@unlike');
Route::post('/posts/{post}/comment', 'CommentsController@store');
Route::post('/users/{user}/follow', 'FollowingsController@follow');
Route::post('/users/{user}/unfollow', 'FollowingsController@unfollow');
