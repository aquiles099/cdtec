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
Route::post('farms/update/{id}', 'FarmController@update');
Route::get('farms/{id}/zones', 'FarmController@zones');
Route::get('farms/{id}/hydraulics', 'FarmController@hydraulics');
Route::get('farms/{id}/nodes', 'FarmController@nodes');
Route::get('farms/{id}/pumpsystems', 'FarmController@pumpsystems');
Route::get('farms/{id}/irrigations', 'FarmController@irrigations');
Route::post('farms/{id}/alarms/triggered', 'FarmController@alarmsTriggered');
Route::get('farms/{id}/realirrigations', 'FarmController@realIrrigations');
Route::get('farms/{id}/measures', 'FarmController@measures');
Route::post('farms/{id}/webhook', 'FarmController@webhookUpdate');
// zones
Route::post('zones/store', 'ZoneController@store');
Route::get('zones/{id}', 'ZoneController@get');
Route::post('zones/update/{id}', 'ZoneController@update');
Route::get('zones/{id}/measures', 'ZoneController@measures');
Route::get('zones/{id}/irrigations', 'ZoneController@irrigations');
Route::get('zones/{id}/hydraulics', 'ZoneController@hydraulics');
Route::post('zones/{id}/alarms/triggered', 'ZoneController@alarmsTriggered');
Route::get('zones/{id}/realirrigations', 'ZoneController@realIrrigations');
// measures
Route::post('measures/store', 'MeasureController@store');
// measures
Route::post('pumpsystems/store', 'PumpSystemController@store');
Route::get('pumpsystems/{id}/zones', 'PumpSystemController@zones');

// nodes
Route::post('nodes/store', 'NodeController@store');
// alarms
Route::post('alarms/store', 'AlarmController@store');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
