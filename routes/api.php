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

Route::middleware('auth:api')->get('/bus-stops', 'BusStopController@all');
Route::middleware('auth:api')->get('/bus-stops/nearby', 'BusStopController@nearby');
Route::middleware('auth:api')->get('/bus-stops/{code}/services', 'BusStopController@services');
Route::middleware('auth:api')->get('/bus-stops/refresh', 'BusStopController@refresh');

Route::middleware('auth:api')->get('/buses', 'BusController@all');
Route::middleware('auth:api')->post('/buses', 'BusController@store');
Route::middleware('auth:api')->post('/buses/{id}', 'BusController@update');
Route::middleware('auth:api')->delete('/buses/{id}', 'BusController@destroy');
