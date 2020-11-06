<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WxController extends Controller
{

	//处理事件推送
    public function wxEvent(){

    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
	
    $token = env('WX_TOKEN');
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );
    
    if( $tmpStr == $signature ){  //验证通过

    	//1/接收数据
    	$xml_str = file_get_contents("php://input");

    	//记录日志
    	file_put_contents('wx_event.log',$xml_str);
    	echo "";
    	die;

    	

    }else{
        echo "";
    }
    }
}
