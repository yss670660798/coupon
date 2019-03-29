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

Route::group(['middleware' => [],'namespace'=>'client','prefix' => 'client'], function () {

    Route::post('/order/detail', 'CashierController@index');

});


Route::group(['middleware' => [],'namespace'=>'Api','prefix' => 'img'], function () {

    Route::post('/dataset/detail', 'DataSetController@detail');
    Route::post('/model/version/create', 'ModelController@createUpload');

});

