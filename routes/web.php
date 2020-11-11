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
// Route::post('/','WxController@index');//接入
// Route::post('/','WxController@wxEvent');

Route::get('/token','WxController@getAccessToken');//获取access_token

Route::get('/createmenu','WxController@createmenu');//自定义菜单

});

Route::get('/wx','WxController@event');//关注回复//接收事件推送

Route::prefix('/test')->group(function(){
	Route::get('/guzzle1','TestController@guzzle1');
	Route::get('/guzzle2','Weixin\IndexController@guzzle2');
});