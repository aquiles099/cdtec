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

Route::group(['middleware' => ['cors'] ], function() {
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
	Route::get('zones/{id}/realIrrigations', 'ZoneController@realIrrigations');
	// paths
	Route::post('path/store', 'PathController@store');
	// bounds
	Route::post('bound/southwest/store', 'BoundController@storeSouthWestBound');
	Route::post('bound/northeast/store', 'BoundController@storeNorthEastBound');
	// measures
	Route::post('measures/store', 'MeasureController@store');
	Route::get('measures/{id}', 'MeasureController@get');
	Route::get('measures/{id}/data', 'MeasureController@data');
	// pumpsystems
	Route::post('pumpsystems/store', 'PumpSystemController@store');
	Route::get('pumpsystems/{id}', 'PumpSystemController@get');
	Route::get('pumpsystems/{id}/zones', 'PumpSystemController@zones');
	Route::get('pumpsystems/{id}/irrigations', 'PumpSystemController@irrigations');
	Route::get('pumpsystems/{id}/realirrigations', 'PumpSystemController@realIrrigations');
	Route::get('pumpsystems/{id}/tanks', 'PumpSystemController@tanks');
	// hydraulics
	Route::get('hydraulics/{id}', 'HydraulicController@get');
	Route::post('hydraulics/{id}', 'HydraulicController@store');
	// irrigations
	Route::post('irrigations/store', 'IrrigationController@store');
	Route::get('irrigations/{id}', 'IrrigationController@get');
	Route::post('irrigations/{id}', 'IrrigationController@updateAction');
	Route::post('irrigations/update/{id}', 'IrrigationController@update');
	Route::delete('irrigations/{id}', 'IrrigationController@delete');
	Route::get('irrigations/{id}/realirrigations', 'IrrigationController@realIrrigations');
	// realIrrigations
	Route::post('realirrigations/store', 'RealIrrigationController@store');
	Route::get('realirrigations/{id}', 'RealIrrigationController@get');
	// nodes
	Route::post('nodes/store', 'NodeController@store');
	Route::get('nodes/{id}/measures', 'NodeController@measures');
	Route::get('nodes/{id}', 'NodeController@get');
	// alarms
	Route::post('alarms/store', 'AlarmController@store');
	// PhysicalConnection
	Route::post('physicalconnection/store', 'PhysicalConnectionController@store');
	// volumes
	Route::post('volumes/store', 'VolumeController@store');
	// polygons
	Route::post('polygons/store', 'PolygonController@store');
	// users
	Route::get('users', 'UserController@all');
	Route::get('users/get/{id}', 'UserController@get');
	Route::post('users/store', 'UserController@store');
	Route::post('users/update/{id}', 'UserController@update');
	Route::delete('users/delete/{id}', 'UserController@delete');
	// farmscamps
	Route::get('farmscamps', 'FarmsUsersController@all');
	Route::get('farmscamps/get/{id}', 'FarmsUsersController@get');
	Route::post('farmscamps/store', 'FarmsUsersController@store');
	Route::post('farmscamps/update/{id}', 'FarmsUsersController@update');
	Route::delete('farmscamps/delete/{id}', 'FarmsUsersController@delete');		
	// roles
	Route::get('roles', 'RoleController@all');
	Route::get('roles/get/{id}', 'RoleController@get');
	Route::post('roles/store', 'RoleController@store');
	Route::post('roles/update/{id}', 'RoleController@update');
	Route::delete('roles/delete/{id}', 'RoleController@delete');		
});

// types
Route::post('types/store', 'TypeController@store');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
