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

Route::post('/auth', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');

Route::group(['middleware' => 'jwt.auth'], function() {
    Route::get('search', 'SearchController@search');
});
