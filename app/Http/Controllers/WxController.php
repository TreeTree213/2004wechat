<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
class WxController extends Controller
{

 public function getAccessToken(){

    $key = 'WX:access_token';

    //检查是否有token
    $token = Redis::get($key);
    if($token){
        echo "有缓存";echo'</br>';
        
    }else{
       
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env('WX_APPSECRET');


        // //使用guzzle发送get请求
        // $client = new Client();  //实例化客户端
        // $response = $client->request('GET',$url,['verify'=>false]);     //发送请求并接受响应

        // $json_str = $response->getBody();          //服务器的响应数据




 

    $data = json_decode($json_str,true);
    $token = $data['access_token'];


    //保存到redis 中  时间为3600

    Redis::set($key,$token);
    Redis::expire($key,3600);

    }
    
    return $token;
 }

	

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
    
    if( $tmpStr == $signature ) {
             //1、接收数据
            $xml_data = file_get_contents("php://input");
            //记录日志
            file_put_contents('wx_event.log',$xml_data);

            //2、把xml文本转换成为php的对象或数组
            $data = simplexml_load_string($xml_data,'SimpleXMLElement',LIBXML_NOCDATA);

              if($data->MsgType=="event"){
                 if($data->Event=="subscribe"){
                   $access_token = $this->getAccessToken();
                    $openid = $data->FromUserName;
           $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
                    $user = file_get_contents($url);
                    $res = json_decode($user,true);

                     if(isset($res['errcode'])){
                        file_put_contents('wx_event.log',$res['errcode']);
                    }else{
                        $user_id = User_info::where('openid',$openid)->first();
                         if($user_id){
                            $user_id->subscribe=1;
                            $user_id->save();
                            $contentt = "感谢再次关注";
                        }else{

                             $res = [
                                'subscribe'=>$res['subscribe'],
                                'openid'=>$res['openid'],
                                'nickname'=>$res['nickname'],
                                'sex'=>$res['sex'],
                                'city'=>$res['city'],
                                'country'=>$res['country'],
                                'province'=>$res['province'],
                                'language'=>$res['language'],
                                'headimgurl'=>$res['headimgurl'],
                                'subscribe_time'=>$res['subscribe_time'],
                                'subscribe_scene'=>$res['subscribe_scene']

                            ];
                            User_info::insert($res);
                            $contentt = "欢迎老铁关注";
                 }
              }
        }

             //取消关注
                if($data->Event=='unsubscribe'){
                    $user_id->subscribe=0;
                    $user_id->save();
                }
                echo $this->responseMsg($data,$contentt);

                    }
        }else{
            echo "";
  }
}

 //关注回复
    public function responseMsg($array,$Content){
                $ToUserName = $array->FromUserName;
                $FromUserName = $array->ToUserName;
                $CreateTime = time();
                $MsgType = "text";

                $text = "<xml>
                  <ToUserName><![CDATA[%s]]></ToUserName>
                  <FromUserName><![CDATA[%s]]></FromUserName>
                  <CreateTime>%s</CreateTime>
                  <MsgType><![CDATA[%s]]></MsgType>
                  <Content><![CDATA[%s]]></Content>
                </xml>";
                echo sprintf($text,$ToUserName,$FromUserName,$CreateTime,$MsgType,$Content);




}
}
