<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
use App\Models\User_info;
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


        //使用guzzle发送get请求
        $client = new Client();  //实例化客户端
        $response = $client->request('GET',$url,['verify'=>false]);     //发送请求并接受响应

        $json_str = $response->getBody();          //服务器的响应数据




 

    $data = json_decode($json_str,true);
    $token = $data['access_token'];


    //保存到redis 中  时间为3600

    Redis::set($key,$token);
    Redis::expire($key,3600);

    }
    
    return $token;
 }


 //上传素材
    public function guzzle2(){
        $access_token = $this->getAccessToken();
        $type         = 'image';
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type='.$type;

         //使用guzzle发送get请求
        $client = new Client();  //实例化客户端
        $response = $client->request('POST',$url,[
            'verify' => false,
            'multipart' => [
            [

             'name' => 'media',
             'contents' => fopen('xunrou.jpg','r')
             ],  //上传文件的路径

        ]

        ]);     //发送请求并接受响应

        $data = $response->getBody();
        echo $data;
    }

     public function createmenu(){

    $access_token = $this->getAccessToken();

  $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;


  $menu = [

        'button' => [

              [
            'name'=>'发送图片',
            'sub_button'=>[

        [
            'type' => 'pic_sysphoto',
            'name' => '拍照',
            'key'  => 'rselfmenu_1',
            'sub_button' => []
        ],

         [
            'type' => 'pic_photo_or_album',
            'name' => '拍照或相册',
            'key'  => 'rselfmenu_2',
            'sub_button' => []
        ],

         [
            'type' => 'pic_weixin',
            'name' => '微信相册',
            'key'  => 'rselfmenu_3',
            'sub_button' => []
        ]

   ]
],

            [
                'name'=>'工具',
                'sub_button'=>[
             [
                    'type'=>'view',
                    'name'=>'品优购',
                    'url'=>'http://laravel.mayatong.top/'

               ],

               [
                'type' => 'click',
                'name' => '天气',
                'key'  => '10086'

               ],
               [
               	'type' => 'click',
               	'name' => '签到',
               	'text' => '签到成功'

               ]
           ]

            ],

            [
            'type' => 'view',
            'name' => 'BILIBILI',
            'url'  => 'http://www.bilibili.com'
        ],
        ]
  ];

         //使用guzzle发送get请求
        $client = new Client();  //实例化客户端
        $response = $client->request('POST',$url,[
            'verify' => false,
            'body' => json_encode($menu,JSON_UNESCAPED_UNICODE)

        ]);     //发送请求并接受响应

        $data = $response->getBody();
        echo $data;
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
        }
        //天气
if($data->MsgType=="text"){
    $city = urlencode(str_replace("天气:","",$data->Content));
    $key = "e2ca2bb61958e6478028e72b8a7a8b60";
    $url = "http://apis.juhe.cn/simpleWeather/query?city=".$city."&key=".$key;
    $tianqi = file_get_contents($url);
    //file_put_contents('tianqi.txt',$tianqi);
    $res = json_decode($tianqi,true);
    $content="";
    if($res['error_code']==0){
        $today = $res['result']['realtime'];
        $content .= "查询天气的城市:".$res['result']['city']."\n";
        $content .= "天气详细情况".$today['info']."\n";
        $content .= "温度".$today['temperature']."\n";
        $content .= "湿度".$today['humidity']."\n";
        $content .= "风向".$today['direct']."\n";
        $content .= "风力".$today['power']."\n";
        $content .= "空气质量指数".$today['aqi']."\n";

        //获取一个星期的天气
        $future = $res['result']['future'];
        foreach($future as $k=>$v){
            $content .= "日期:".date("Y-m-d",strtotime($v['date'])).$v['temperature'].",";
            $content .= "天气:".$v['weather']."\n";
        }
    }else{
        $content = "你查寻的天气失败，请输入正确的格式:天气、城市";
    }
    //file_put_contents("tianqi.txt",$content);

    echo $this->responseMsg($data,$content);   

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



   public function dlmedia(){
   	
   }
}
