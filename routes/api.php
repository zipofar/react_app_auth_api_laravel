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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', 'Auth\ApiLoginController@login');
Route::post('/register', 'Auth\ApiRegisterController@register');
Route::get('/news', 'NewsController@index');
Route::get('/countries', 'CountriesController@index');



Route::middleware(['api_auth'])->group(function () {
    Route::put('/profile', 'UserController@update');
    Route::get('/profile', 'UserController@getProfile');
});
