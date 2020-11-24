<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Goods;
class ApiController extends Controller
{
    // public function __construct(){
    // 	app('debugbar')->disable();//关闭调试

    // }

    public function userInfo(){
    	echo __METHOD__;
    }



    public function test(){
    	$res = DB::table('ecs_goods')->get()->toArray();
        return $res;
    	//echo json_encode($goods_info);
    }


    public function goodslist(Request $request){
       // $g = Goods::select('goods_id','goods_name','shop_price','goods_img','goods_number')->limit(7)->get()->toArray();
       $page_size = $request->get('ps');
       $g = Goods::select('goods_id','goods_name','shop_price','goods_img','goods_number','market_price')->paginate($page_size);

        $response = [
            'errno' => 0,
            'msg'   => 'ok',
            'data'  => [
                'list' => $g->items()
            ]
        ];

        return $response;
    }

       public function detail(Request $request){
        $goods_id = $request->get('goods_id');
        $detail = Goods::select('goods_id','goods_name','shop_price','goods_img','goods_number','market_price','goods_thumb')->where('goods_id',$goods_id)->first()->toArray();
  
        // $response = [
        //     'error'=>0,
        //     'msg'=>'ok',
        //     'data'=>[
        //         'list'=>$detail
        //     ]
        // ];
        $array=[
            'goods_imgs' => explode(",",$detail['goods_thumb']),
            'goods_name' => $detail['goods_name'],
            'shop_price' => $detail['shop_price'],
            'goods_number'=>$detail['goods_number'],
            'market_price'=>$detail['market_price'],
        ];
        return $array;
    }

}
