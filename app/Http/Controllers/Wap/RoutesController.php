<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/6/26
 * Time: 18:08
 */

namespace App\Http\Controllers\Wap;


use App\Models\ActivityConfig;
use App\Models\Order;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;

class RoutesController extends Controller
{
    //主路由
    public function index()
    {
        $imgId=0;
        $title=null;
        $nowDate=date('Y-m-d H:i:s');

        //查找当前日期的提领活动
        $activity=ActivityConfig::where('start_time','<=',$nowDate)
            ->where('end_time','>=',$nowDate)
            ->where('status',1)
            ->first();

        //如果提领活动存在则跳转提领
        if($activity){
            $imgId=$activity->title_img;
            $this->data['title']=$activity->name;
            $this->data['img_id']=$activity->title_img;
            return view('wap.home.index',$this->data);
        }

        //若当前日期不在提领时间内，则查找最近的一次提领
        $activity=ActivityConfig::where('status',1)
            ->orderByDesc('end_time')
            ->first();

        //若有提领活动，则判断当前日期是在提领之前还是之后
        if($activity){
            //判断是否处于提领之前
            if($nowDate<strtotime($activity->start_time)){
                $imgId=$activity->notice_img;
            }
            //判断是否处于提领结束并在提领结束10天之内
            if(strtotime($nowDate)>strtotime($activity->end_time) && strtotime($nowDate)<strtotime('+10 day',strtotime($activity->end_time))){
                $imgId=$activity->end_img;
            }
            //判断是否处于提领结束后10天之后
            if(strtotime($nowDate)>strtotime('+10 day',strtotime($activity->end_time))){
                $imgId=0;
            }
        }

        return view();
    }

    //提领
    public function activity()
    {

    }

    //展示图片
    public function img()
    {

    }

    //订单
    public function order()
    {
        $this->data['url']=config('app.url');
        return view('wap.order.index', $this->data);
    }

    //订单详情
    public function orderDetail(Request $request,$id)
    {

        $order=Order::find($id);
        $this->data['order']=$order;
        $this->data['statusName']=['','已确认','已发货','已完成'];
        return view('wap.order.detail', $this->data);
    }


}