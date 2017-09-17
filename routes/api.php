<?php

use Illuminate\Http\Request;

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

Route::post('/auth/facebook', 'Auth\LoginController@loginWithFacebook');
Route::post('/auth', 'Auth\LoginController@authenticate');
Route::post('/logout', 'Auth\LoginController@logout');
Route::post('/register', 'Auth\RegisterController@store');
Route::post('/forgot-password', 'Auth\ForgotPasswordController@store');
Route::post('/reset-password', 'Auth\ResetPasswordController@update');

Route::group(['middleware' => ['jwt.auth', 'getUserFromToken']], function () {
    Route::resource('books', 'BookController', ['only' => ['index', 'show']]);
    Route::resource('bookshelfs', 'BookshelfController', ['only' => ['index', 'store', 'destroy']]);
    Route::get('search', 'SearchController@search');

    Route::group(['prefix' => 'user'], function () {
        Route::patch('/profile/update/{id}', 'UserController@updateProfile');
    });
});
