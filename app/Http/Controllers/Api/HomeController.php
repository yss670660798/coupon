<?php
/**
 * Created by PhpStorm.
 * User: hongpo
 * Date: 2018/8/13
 * Time: 16:50
 */

namespace App\Http\Controllers\Api;


use App\Libs\appJMessage;
use App\Libs\ReturnCode;
use App\Models\Goods;
use App\Models\Coupon;
use App\Models\Home;
use App\Models\Store;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    //1.管理员，2.品牌
    public function getHome(Request $request)
    {
        $type=1;
        $brand_id=$request->user()->brand_id;
        if($brand_id){
            $type=2;
        }
        $top1=0;$top2=0;$top3=0;$top4=0;
        if($type==1){
            //1.上线品牌数
            $top1=Goods::where('parent_id',0)->count();
            //2.门店数
            $top2=Store::where('status',1)->count();
            //3.设备数
            $top3=Coupon::count();
            //4.在线设备数
            $equipment=Coupon::get();

            foreach ($equipment as $item){
                $stat=appJMessage::stat($item->jg_name);
                $online=isset($stat['body']['online'])?$stat['body']['online']:false;
                if($online){
                    $top4++;
                }
            }

        }else{
            //1.上线门店数
            $top1=Store::where('brand_id',$brand_id)->where('status',1)->count();
            //2.正常设备数 3.即将到期设备数 4.异常设备数
            $equipment=Coupon::where('brand_id',$brand_id)->get();

            foreach ($equipment as $item){
                $stat=appJMessage::stat($item->jg_name);
                $online=isset($stat['body']['online'])?$stat['body']['online']:false;
                if($online){
                    $top2++;
                }else{
                    $top4++;
                }
                //3.即将到期设备数
                $end_date=$item->end_date;
                if($end_date){
                    $second=(time()- strtotime($end_date));
                    if($second>=(3*24*60*60)){
                        $top3++;
                    }
                }
            }
        }

        $response['top1']=$top1;
        $response['top2']=$top2;
        $response['top3']=$top3;
        $response['top4']=$top4;

        //判断home表是否存在记录
        $count=Home::where('brand_id',$brand_id)->count();
        $data=[
            'brand_id'=>$brand_id,
            'val1'=>$top1,
            'val2'=>$top2,
            'val3'=>$top3,
            'val4'=>$top4
        ];
        if($count>0){
            Home::where('brand_id',$brand_id)->update($data);
        }else{
            Home::insert($data);
        }

        return response(ReturnCode::success($response));
    }

    public function getHomeDetail(Request $request)
    {
        $brand_id=$request->user()->brand_id;
        $count=Home::where('brand_id',$brand_id)->count();

        $data=[
            'brand_id'=>$brand_id,
            'top1'=>0,
            'top2'=>0,
            'top3'=>0,
            'top4'=>0
        ];
        if($count>0){
            $home=Home::where('brand_id',$brand_id)->first();

            if($home){
                $data['top1']=intval($home->val1);
                $data['top2']=intval($home->val2);
                $data['top3']=intval($home->val3);
                $data['top4']=intval($home->val4);
            }
        }
        return response(ReturnCode::success($data));
    }

    public function getUser(Request $request)
    {
        $user=$request->user();
        if(!$user){
            return response(ReturnCode::error(1007,'未登录'));
        }

        $user['brand_name']=null;
        $user['store_name']=null;
        $user['role_name']=null;

        $brand_id=$user->brand_id;
        if($brand_id){
            $brand=Goods::find($brand_id);
            if($brand){
                $user->brand_name=$brand->name;
            }
        }
        $store_id=$user->store_id;
        if($store_id){
            $store=Store::find($store_id);
            if($store){
                $user->store_name=$store->name;
            }
        }
        $role_id=$user->role_id;
        if($role_id){
            $role=UserRole::find($role_id);
            if($role){
                $user->role_name=$role->name;
            }
        }

        $user->password=null;

        return response(ReturnCode::success($user));
    }
}