<?php

use Illuminate\Support\Facades\Route;
use Atymic\Twitter\Facade\Twitter;

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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function(){
    
    Route::get('/home', 'App\Http\Controllers\TwitterController@home')->name('home');
    Route::post('/tweet', 'App\Http\Controllers\TwitterController@tweet')->name('tweet');
    Route::post('/like','App\Http\Controllers\TwitterController@like')->name('like');
    Route::post('/unlike','App\Http\Controllers\TwitterController@unlike')->name('unlike');

});




Route::get('/login', 'App\Http\Controllers\TwitterController@login')->name('login');
Route::get('/callback', 'App\Http\Controllers\TwitterController@callback')->name('callback');
Route::post('/logout', 'App\Http\Controllers\TwitterController@logout')->name('logout');





