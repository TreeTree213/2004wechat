<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
class TestController extends Controller
{
    public function test1()
    {

    	echo __METHOD__;

    	$list = DB::table('user')->limit(3)->get();
    	dd($list);

    	// $key = 'wx2004';
    	// Redis::set($key,time());
    	// echo Redis::get($key);

    }


    public function test2(){
    	echo __METHOD__;
    }



    public function test3(){
        //echo '<pre>';print_r($_POST);echo '</pre>';
        $xml_str = file_get_contents("php://input");
       

       //将xml转换为 对象或数组
       $xml_obj = simplexml_load_string($xml_str);
       // echo '<pre>';print_r($xml_str);echo '</pre>';
       echo $xml_obj->ToUserName;
    }


    public function guzzle1(){
        //echo __METHOD__;

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env('WX_APPSECRET');

        //使用guzzle发送get请求
        $client = new Client();  //实例化客户端
        $response = $client->request('GET',$url,['verify'=>false]);     //发送请求并接受响应

        $json_str = $response->getBody();          //服务器的响应数据

        echo $json_str;



    }


 
}
