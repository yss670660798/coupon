<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/11/28
 * Time: 9:53
 */

namespace App\Http\Controllers\Api;


use App\Libs\ReturnCode;
use App\Models\Coupon;
use App\Models\CouponCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CouponDetailController extends Controller
{
    /**
     * 卡券明细列表
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/11/28 9:58
     */
    public function index(Request $request,$id)
    {
        $coupon=Coupon::find($id);
        if(!$coupon){
            return response(ReturnCode::success([]));
        }

        $limit=$request->input('limit',10);
        $keyword=$request->input('keyword',null);
        $status=$request->input('status',null);

        $where=function ($q)use($id,$keyword,$status){
              $q->where('coupon_id',$id);
              if(!empty($keyword)){
                  $q->where('sn','like','%'.$keyword.'%');
              }
              if(!empty($status)){
                    if($status==1){
                        $q->where('is_used',0)->where('is_void',0);
                        $q->where('expired_at','>',date('Y-m-d H:i:s'));
                    }
                    if($status==2){
                        $q->where('is_used',1)->where('is_void',0);
                    }
                    if($status==3){
                        $q->where('is_used',0)->where('is_void',1);
                    }
                    if($status==4){
                        $q->where('expired_at','<',date('Y-m-d H:i:s'));
                    }
              }
        };

        $detail=CouponCard::where($where)
            ->with(['coupon'=>function($q){
                $q->select(['id','name']);
            }])
            ->paginate($limit);

        foreach ($detail as $item){
            $item->status=1;
            if(time()>strtotime($item->expired_at)){
                $item->status=4;
            }
            if($item->is_used==1){
                $item->status=2;
            }
            if($item->is_void==1){
                $item->status=3;
            }
        }

        $detail=$detail->toArray();

        $response           = ReturnCode::success($detail['data']);
        $response['_count'] = $detail['total'];

        return response($response);
    }

    /**
     * 删除卡券
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/15 21:20
     */
    public function delete(Request $request,$id)
    {
        $card=CouponCard::find($id);
        if(!$card){
            return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'卡券不存在'));
        }

        if($card->is_used==1){
            return response(ReturnCode::error(ReturnCode::FORBIDDEN,'卡券已使用无法删除'));
        }

        try{
            DB::beginTransaction();
            $card->delete();
            DB::commit();
            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }

    /**
     * 修改有效期
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/15 21:28
     */
    public function restart(Request $request,$id)
    {
        $expired_ar=$request->input('expired_at',date('Y-m-d H:i:s'));
        try{
            $card=CouponCard::find($id);
            if(!$card){
                return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'卡券不存在'));
            }

            if($card->is_used==1){
                return response(ReturnCode::error(ReturnCode::FORBIDDEN,'卡券已使用，无法修改'));
            }

            if($card->is_void==1){
                return response(ReturnCode::error(ReturnCode::FORBIDDEN,'卡券已作废，无法修改'));
            }

            DB::beginTransaction();
            $card->expired_at=date('Y-m-d 23:59:59',strtotime($expired_ar));
            $card->save();
            DB::commit();

            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }

    /**
     * 卡券作废
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/15 21:31
     */
    public function stopCoupon(Request $request,$id)
    {
        try{
            $card=CouponCard::find($id);
            if(!$card){
                return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'卡券不存在'));
            }

            if($card->is_used==1){
                return response(ReturnCode::error(ReturnCode::FORBIDDEN,'卡券已使用，无法修改'));
            }

            DB::beginTransaction();
            $card->is_void=1;
            $card->void_at=date('Y-m-d H:i:s');
            $card->save();
            DB::commit();
            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }
}