<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use DB;
use App\Models\Xcx;
class XcxController extends Controller
{
    public function login(Request $request){

    	//接收code
    	$code = $request->get('code');


    	//使用code
    	$url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.env('WX_XCX_APPID').'&secret='.env('WX_XCX_APPSECRET').'&js_code='.$code.'&grant_type=authorization_code';


    	// $response = file_get_contents($url);
    	$data = json_decode(file_get_contents($url),true);
    	

    	//自定义登录状态
    	if(isset($data['errcode']))  //错误
    	{
    		//TODO 错误处理
    		$response = [

    			'errno' => 50001,
    			'msg'   => '登陆失败'
    		];
    	}else{   //成功
    	if(empty(Xcx::where('openid',$data['openid'])->first())){
		    $openid=["openid"=>$data["openid"]];
		    Xcx::insert($openid);
		}
    		$token = sha1($data['openid'] . $data['session_key'].mt_rand(0,999999));
    		//保存token到Redis
    		$redis_key = 'xcx_token:'.$token;
    		Redis::set($redis_key,time());
    		//设置过期时间
    		Redis::expire($redis_key,7200);

    		$response = [

    			'errno' => 0,
    			'msg'   => 'ok',
    			'data'  => [
    				'token' => $token
    			]
    		];
    	}
    	return $response;
    }
}
