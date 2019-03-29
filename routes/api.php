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

//回调函数 ricky
Route::group(['middleware' => [],'namespace'=>'Api','prefix' => 'client'], function () {
    //回调
    Route::post('/callback', 'ClientController@callback');

    //注册
    Route::post('/register', 'ClientController@register');

    //登录认证
    Route::post('/auth', 'ClientController@auth');

    //模型下载
    Route::get('/model/download', 'ClientController@downloadModel');

    //配置信息
    Route::post('/setConfig', 'ClientController@setEqpConfig');
    Route::post('/getConfig', 'ClientController@getEqpConfig');

    //设置状态
    Route::post('/setStatus', 'ClientController@setStatus');
    Route::get('/test', 'ClientController@test');
//    Route::get('/teste', 'ClientController@teste');
});
//Jerry 使用
Route::group(['middleware' => [],'namespace'=>'Server','prefix' => 'server'], function () {

    //拉取队列数据-x
    Route::get('/getModelQueue', 'CallServerController@getModelQueue');
    //回执状态
    Route::post('/postStatus', 'CallServerController@postStatus');
    //下发模型
    Route::post('/send','CallServerController@sendModel');
    //FTP遍历接口
    Route::get('/ergodic','ErgodicController@ergodic');
    //得到空版本参数
    Route::get('/version/params','CallServerController@getModelVersionParams');

    //模型下载接口
    Route::get('/brand','ErgodicController@getBrand');
    Route::get('/model','ErgodicController@getModelCode');
    Route::get('/model/version','ErgodicController@getModelVersion');


    //拉取路径和SKU-X
    Route::get('/getAttach', 'CallServerController@getAttach');
    Route::get('/chunk', 'CallServerController@chunk');
    Route::get('/params', 'CallServerController@getParams');

});
