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
Route::get('farms/{id}/zones', 'FarmController@zones');
Route::get('farms/{id}/hydraulics', 'FarmController@hydraulics');
Route::get('farms/{id}/pumpsystems', 'FarmController@pumpsystems');
Route::get('farms/{id}/measures', 'FarmController@measures');
// zones
Route::post('zones/store', 'ZoneController@store');
// measures
Route::post('measures/store', 'MeasureController@store');
// measures
Route::post('pumpsystems/store', 'PumpSystemController@store');
// nodes
Route::post('nodes/store', 'NodeController@store');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
