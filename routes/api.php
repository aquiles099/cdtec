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
// accounts
Route::get('accounts', 'AccountController@all');
Route::get('accounts/{id}', 'AccountController@get');
Route::post('accounts/store', 'AccountController@store');
Route::post('accounts/update/{id}', 'AccountController@update');
// farms
Route::get('farms', 'FarmController@all');
Route::get('farms/{id}', 'FarmController@get');
Route::post('farms/store', 'FarmController@store');
// Route::post('farms/update/{id}', 'FarmController@update');
// zones
Route::get('zones', 'FarmController@all');
Route::get('zones/{id}', 'FarmController@get');
Route::post('zones/store', 'FarmController@store');
// Route::post('zones/update/{id}', 'FarmController@update');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
