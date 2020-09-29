<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();


Route::get('/posts/search', 'PostsController@search')->name('posts.search')->middleware('auth');
Route::get('/', 'PostsController@index')->name('posts.index')->middleware('auth');


Route::resource('posts', 'PostsController')->except([
    'index'
])->middleware('auth');
    
Route::resource('users', 'UsersController')->middleware('auth');
