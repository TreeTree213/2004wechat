<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
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

    	//1、接收数据
    	$xml_str = file_get_contents("php://input") . "\n\n";

    	//记录日志
    	file_put_contents('wx_event.log',$xml_str,FILE_APPEND);

    	//将接受的数据转化为对象
    	$obj = simplexml_load_string($xml_str);//将文件转换为对象

    	if($obj->MsgType =="event"){
    		if($obj->Event == "subscribe")
    		$content="欢迎关注";
    		echo $this->huifu($obj,$content);
    	}
    }

    	echo "";
    	die;

    	

    }else{
        echo "";
    }
    }

  public function huifu($obj,$content){
  	$ToUserName = $obj->FromUserName;
  	$FromUserName = $obj->ToUserName;
  	$time = time();


  	$xml = "<xml>
	  <ToUserName><![CDATA[".$ToUserName."]]></ToUserName>
	  <FromUserName><![CDATA[".$FromUserName."]]></FromUserName>
	  <CreateTime>time()</CreateTime>
	  <MsgType><![CDATA[%s]]></MsgType>
	  <Content><![CDATA[".$content."]]></Content>
	  <MsgId>%s</MsgId>
	  </xml>";

	  echo $xml;
  }

}
