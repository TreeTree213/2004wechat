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
Route::post('/wx','WxController@wxEvent');//接收事件推送

Route::get('/weixin/token','Weixin\IndexController@getAccessToken');//获取access_token



Route::prefix('/test')->group(function(){
	Route::get('/guzzle1','TestController@guzzle1');
	Route::get('/guzzle2','Weixin\IndexController@guzzle2');
});