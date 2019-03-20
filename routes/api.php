<?php

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

Route::group(['namespace' => 'Auth'], function () {
    Route::post('/auth/facebook', 'LoginController@loginWithFacebook');
    Route::post('/auth', 'LoginController@authenticate');
    Route::post('/register', 'RegisterController@store');
    Route::post('/forgot-password', 'ForgotPasswordController@store');
    Route::post('/reset-password', 'ResetPasswordController@update');
});

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::resource('books', 'BookController', ['only' => ['index', 'show']]);
    Route::resource('bookshelfs', 'BookshelfController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::get('notifications/count', 'NotificationController@count');
    Route::get('notifications', 'NotificationController@index');
    Route::resource('hearts', 'HeartController', ['only' => ['store', 'update', 'destroy']]);
    Route::resource('messages', 'MessageController', ['except' => ['index', 'create', 'edit']]);
    Route::get('chats', 'ChatController@index');
    Route::get('search', 'SearchController@search');

    Route::group(['prefix' => 'user'], function () {
        Route::put('profile/password', 'ProfileController@updatePassword');
        Route::put('profile', 'ProfileController@update');
        Route::get('profile', 'ProfileController@me');
        Route::get('/{slug}', 'ProfileController@show');
    });

    Route::post('/logout', 'Auth\LoginController@logout');
});

Route::group(['middleware' => ['jwt.refresh']], function () {
    Route::get('refresh-token', 'Auth\RefreshTokenController@index');
});
