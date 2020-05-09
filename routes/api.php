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

Route::middleware(['auth:api', 'checkViewBusStop'])->get('/bus-stops', 'BusStopController@all');
Route::middleware(['auth:api', 'checkRefreshBusStop'])->post('/bus-stops', 'BusStopController@refresh');

Route::middleware(['auth:api', 'checkViewService'])->get('/services/{code}', 'ServiceController@services');

Route::middleware(['auth:api', 'checkAllBuses'])->get('/buses', 'BusController@all');
Route::middleware(['auth:api', 'checkBusFields'])->post('/buses', 'BusController@store');
Route::middleware(['auth:api', 'checkUpdateBus'])->post('/buses/{id}', 'BusController@update');
Route::middleware(['auth:api', 'checkDeleteBus'])->delete('/buses/{id}', 'BusController@destroy');
Route::middleware(['auth:api', 'checkViewBus'])->get('/buses/{id}/arrival', 'BusController@arrival');
