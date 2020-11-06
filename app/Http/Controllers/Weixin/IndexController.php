<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class IndexController extends Controller
{
    public function index(){
    	$result = $this->checkSignature();
    	if($result){
    		echo $_GET["echostr"];
    		exit;
    	}
    }

    private function checkSignature(){

    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
	
    $token = config('weixin.Token');
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );
    
    if( $tmpStr == $signature ){
        return true;
    }else{
        return false;
    }
}

//获取access_token
 public function getAccessToken(){

 	$key = 'WX:access_token';

 	//检查是否有token
 	$token = Redis::get($key);
 	if($token){
 		echo "有缓存";echo'</br>';
 		
 	}else{
 		echo "无缓存";
 	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env('WX_APPSECRET');

 	$response = file_get_contents($url);

 

 	$data = json_decode($response,true);
 	$token = $data['access_token'];


 	//保存到redis 中  时间为3600

 	Redis::set($key,$token);
 	Redis::expire($key,3600);

 	}
 	
 	echo "access_token: ".$token;
 }

}
