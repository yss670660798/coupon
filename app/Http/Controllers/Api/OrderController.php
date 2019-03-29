<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/12/14
 * Time: 17:38
 */

namespace App\Http\Controllers\Api;


use App\Libs\Express;
use App\Libs\ReturnCode;
use App\Models\CouponCard;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * 订单列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/28 16:14
     */
    public function index(Request $request)
    {
        $limit    = $request->input('limit',10);
        $date     = $request->input('date',null);
        $keyword  = $request->input('keyword',null);
        $goods_id = $request->input('goods_id',null);
        $status   = $request->input('status',null);

        $where=function ($q)use($date,$keyword,$goods_id,$status){
            if(!empty($date)){
                $q->whereBetween('created_at',[date('Y-m-d 0:00:00',strtotime($date)),date('Y-m-d 23:59:59',strtotime($date))]);
            }

            if(!empty($keyword)){
                $q->where(function ($qq)use($keyword){
                    $qq->orWhere('code','like','%'.$keyword.'%');
                    $qq->orWhere('contact','like','%'.$keyword.'%');
                    $qq->orWhere('mobile','like','%'.$keyword.'%');
                    $card=CouponCard::where('sn','like','%'.$keyword.'%')->pluck('id');
                    $qq->orWhereIn('coupon_card_id',$card);
                });
            }

            if(!empty($status)){
                $q->where('status',$status);
            }

            if(!empty($goods_id)){
                $q->where('goods_id',$goods_id);
            }
        };

        $brand=Order::where($where)
            ->orderByDesc('id')
            ->with(['couponCard'=>function($q){
                $q->select(['id','coupon_id','sn']);
                $q->with(['coupon'=>function($qq){
                    $qq->select(['id','name']);
                }]);
            }])
            ->paginate($limit)
            ->toArray();

        $response           = ReturnCode::success($brand['data']);
        $response['_count'] = $brand['total'];

        return response($response);
    }

    /**
     * 填写快递单号
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/28 16:32
     */
    public function updateLogistic(Request $request)
    {
        Log::info($request->all());
        try{
            $id=$request->input('id',null);
            if(empty($id)){
                return response(ReturnCode::error(ReturnCode::PARAMS_ERROR,'参数错误'));
            }

            $shipper_code  = $request->input('shipper_code',null);
            $logistic_code = $request->input('logistic_code',null);

            $order = Order::find($id);
            if(!$order){
                return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'订单不存在'));
            }

            DB::beginTransaction();

            $order->status=3;
            $order->shipper_code=$shipper_code;
            $order->logistic_code=$logistic_code;
            $order->save();

            DB::commit();

            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }

    /**
     * 微信获取个人订单
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/31 17:17
     */
    public function getMyOrder(Request $request)
    {
        $member=$request->session()->get('member',null);
//        if(!$member){
//            return response(ReturnCode::error(ReturnCode::AUTHORIZE_FAIL,'登录过期请刷新重试'));
//        }

        $status=$request->input('status','all');

        $where=function ($q)use($member,$status){
            //$q->where('member_id',$member->id);

            if($status!='all'){
                $q->where('status',$status);
            }
        };

        $orders=Order::where($where)
            ->with(['goods'=>function($q){
                $q->select(['id','name','image_id']);
                $q->with(['img']);
            },'couponCard'=>function($q){
                $q->select(['id','coupon_id']);
                $q->with(['coupon'=>function($qq){
                    $qq->select(['id','name']);
                }]);
            }])
            ->paginate(10)
            ->toArray();

        $response           = ReturnCode::success($orders['data']);
        $response['_count'] = $orders['total'];

        return response($response);
    }

    /**
     * 获得物流信息
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2019/1/8 21:45
     */
    public function getTrans(Request $request,$id)
    {
        try{
            $order=Order::find($id);
            if(!$order){
                return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'订单无效'));
            }

            $traces=[];
            $res=json_decode(Express::getOrderTraces($order->logistic_code,$order->shipper_code),true);
            Log::info($res);
            if(count($res['Traces'])>0){
                $traces=array_reverse($res['Traces']);
                if($res['State']==3){
                    $order->status==3;
                    $order->save();
                }
            }

            return response(ReturnCode::success($traces));
        }catch (\Exception $e){
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }


}