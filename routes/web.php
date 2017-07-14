<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('bookshelf', 'UserController@dashboard');
Route::get('book/{slug}', 'BookController@showBook');
Route::post('save-to-bookshelf', 'BookController@save');
Route::post('remove-from-bookshelf', 'BookController@remove');

Route::get('auth/facebook', 'Auth\LoginController@redirectToProvider');
Route::get('auth/facebook/callback', 'Auth\LoginController@handleProviderCallback');

Auth::routes();
