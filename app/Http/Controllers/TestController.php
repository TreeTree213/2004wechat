<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Redis;
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
}
