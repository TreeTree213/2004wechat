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
    return view('welcome');
});

Route::get('/test1','TestController@test1');
Route::get('/test2','TestController@test2');
Route::post('/test3','TestController@test3');

Route::get('/info',function (){
	phpinfo();
});
//微信接入
Route::prefix('/wx')->group(function(){
Route::post('/','WxController@index');//接入
Route::post('/','WxController@wxEvent');//接收事件推送
Route::get('/token','WxController@getAccessToken');//获取access_token
Route::get('/createmenu','WxController@createmenu');//自定义菜单

Route::get('/xcxlogin','Weixin\XcxController@login');//小程序登录
});


Route::get('/weixin/token','Weixin\IndexController@getAccessToken');//获取access_token
Route::get('/weixin/createmenu','Weixin\IndexController@createmenu');//自定义菜单
Route::get('/weixin/event','Weixin\IndexController@event');//关注回复


Route::prefix('/test')->group(function(){
	Route::get('/guzzle1','TestController@guzzle1');
	Route::get('/guzzle2','Weixin\IndexController@guzzle2');
});


Route::prefix('api')->group(function(){
	Route::get('/userinfo','Weixin\ApiController@userInfo');
	Route::get('/test','Weixin\ApiController@test');
	Route::get('/goodslist','Weixin\ApiController@goodslist');
	Route::get('/goodsdetail','Weixin\ApiController@detail');
});