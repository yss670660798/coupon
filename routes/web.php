<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});


Route::get('/login', function () {
    return view('login.index');
});

//admin页面路由 ,'auth'
Route::group(['middleware' => ['web','auth'],'namespace'=>'Admin','prefix' => ''], function () {

    Route::get('/logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        return redirect('/login');
    });

    Route::get('/home', 'RoutesController@home');

    Route::get('/goods', 'RoutesController@goods');

    Route::get('/sys/user', 'RoutesController@sysUser');
    Route::get('/sys/role', 'RoutesController@sysRole');
    Route::get('/sys/menu', 'RoutesController@sysMenu');
    Route::get('/sys/log', 'RoutesController@sysLog');

    Route::get('/coupon', 'RoutesController@coupon');
    Route::get('/coupon/detail/{id}', 'RoutesController@coupon_detail')->where('id','[0-9]+');

    Route::get('/order', 'RoutesController@order');

    Route::get('/activity', 'RoutesController@activity');
    Route::get('/activity/add', 'RoutesController@activityAdd');
    Route::get('/activity/edit/{id}', 'RoutesController@activityEdit')->where('id','[0-9]+');

});

//手机端页面
Route::group(['middleware' => ['web'],'namespace'=>'Wap','prefix' => 'wap'], function () {

    Route::get('/activity', 'RoutesController@index');

    Route::get('/order', 'RoutesController@order');
    Route::get('/order/detail/{id}', 'RoutesController@orderDetail')->where('id','[0-9]+');
});

//手机端接口
Route::group(['middleware' => ['web'],'namespace'=>'Api','prefix' => 'api'], function () {

    Route::get('/my/order', 'OrderController@getMyOrder');
    Route::get('/my/order/{id}', 'OrderController@getTrans')->where('id','[0-9]+');
});

//登录接口路由  不需要验证路由
Route::group(['middleware' => ['web'],'namespace'=>'Api','prefix' => 'api'], function () {
    Route::post('/login','LoginController@login');

    Route::get('/update/region/code','RegionController@updateRegionCode');
    Route::get('/region/list','RegionController@regionList');
});

//接口路由 'isLogin','Log'
Route::group(['middleware' => ['web'],'namespace'=>'Api','prefix' => 'api'], function () {
    //HOME 2018-8-13
    Route::put('/home/password','LoginController@changePassword');
    Route::get('/home','HomeController@getHome');
    Route::get('/home/detail','HomeController@getHomeDetail');
    Route::get('/home/user','HomeController@getUser');
//====================================================================================================================
    Route::get('/region','RegionController@index');
    Route::get('/store','RegionController@store');
    Route::post('/upload','UploadController@index');
    Route::get('/img/{id}','UploadController@showImg')->where('id','[0-9]+');

    //角色管理
    Route::get('/sys/role','UserRoleController@index');
    Route::post('/sys/role','UserRoleController@add');
    Route::put('/sys/role/{id}','UserRoleController@edit')->where('id','[0-9]+');
    Route::delete('/sys/role/{id}','UserRoleController@delete')->where('id','[0-9]+');
    Route::get('/sys/menu','MenuController@menuList');
    Route::get('/sys/role/list','UserRoleController@getList');

    //产品管理
    Route::get('/goods','GoodsController@index');
    Route::post('/goods','GoodsController@add');
    Route::put('/goods/{id}','GoodsController@edit')->where('id','[0-9]+');
    Route::put('/goods/stock/{id}','GoodsController@addStock')->where('id','[0-9]+');
    Route::delete('/goods/{id}','GoodsController@delete')->where('id','[0-9]+');
    Route::get('/get/goods','GoodsController@getList');
    //用户管理
    Route::get('/sys/user','UserController@index');
    Route::post('/sys/user','UserController@add');
    Route::put('/sys/user/{id}','UserController@edit')->where('id','[0-9]+');
    Route::put('/sys/user/reset/{id}','UserController@resetPwd')->where('id','[0-9]+');
    Route::delete('/sys/user/{id}','UserController@delete')->where('id','[0-9]+');
    //菜单管理
    Route::get('/admin/sys/menu','MenuController@index');
    Route::post('/admin/sys/menu','MenuController@add');
    Route::put('/admin/sys/menu/{id}','MenuController@edit')->where('id','[0-9]+');
    Route::delete('/admin/sys/menu/{id}','MenuController@delete')->where('id','[0-9]+');
    //日志管理
    Route::get('/admin/sys/log','LogController@index');
    Route::delete('/admin/sys/log/{id}','LogController@delete')->where('id','[0-9]+');
    //卡券管理
    Route::get('/coupon','CouponController@index');
    Route::post('/coupon','CouponController@add');
    Route::post('/coupon/import/{id}','CouponController@importCard')->where('id','[0-9]+');
    Route::put('/coupon/{id}','CouponController@edit')->where('id','[0-9]+');
    Route::delete('/coupon/{id}','CouponController@delete')->where('id','[0-9]+');

    //卡券明细
    Route::get('/coupon/detail/{id}','CouponDetailController@index')->where('id','[0-9]+');
    Route::delete('/coupon/card/{id}','CouponDetailController@delete')->where('id','[0-9]+');
    Route::put('/coupon/card/restart/{id}','CouponDetailController@restart')->where('id','[0-9]+');
    Route::put('/coupon/card/stop/{id}','CouponDetailController@stopCoupon')->where('id','[0-9]+');
    //订单
    Route::get('/order','OrderController@index');
    Route::put('/order/logistic','OrderController@updateLogistic');
    Route::get('/order/track/{id}','OrderController@getTrans')->where('id','[0-9]+');

    //提领设置
    Route::get('/activity','ActivityController@index');
    Route::post('/activity','ActivityController@create');
    Route::put('/activity/{id}','ActivityController@update')->where('id','[0-9]+');
    Route::put('/activity/status/{id}','ActivityController@changeStatus')->where('id','[0-9]+');
    Route::delete('/activity/{id}','ActivityController@delete')->where('id','[0-9]+');
    Route::get('/activity/info/{id}','ActivityController@info')->where('id','[0-9]+');

});