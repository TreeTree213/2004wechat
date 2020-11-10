<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
class IndexController extends Controller
{
   //获取access_token
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
            'type' => 'click',
            'name' => 'wx2004',
            'key'  => 'k_wx2004'
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
            'body' => json_encode($menu)

        ]);     //发送请求并接受响应

        $data = $response->getBody();
        echo $data;
  }



}
