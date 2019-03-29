<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/7/11
 * Time: 14:23
 */

namespace App\Http\Controllers\Api;


use App\Libs\appJMessage;
use App\Libs\ReturnCode;
use App\Models\Coupon;
use App\Models\CouponCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CouponController extends Controller
{
    /**
     * 卡券列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/7/11 14:31
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword',null);
        $good_id = $request->input('good_id',null);
        $limit   = $request->input('limit',10);

        $where=function ($q)use($keyword,$good_id){
            if(!empty($keyword)){
                $q->where('name','like','%'.$keyword.'%');
            }

            if($good_id){
                $q->where('goods_id',$good_id);
            }
        };

        $coupon=Coupon::where($where)
            ->orderByDesc('id')
            ->with(['good'=>function($q){
                $q->select(['id','name']);
            }])
            ->paginate($limit);


        foreach ($coupon as $item){
            $item->used_count=CouponCard::where('coupon_id',$item->id)->where('is_used',1)->count();
            $item->void_count=CouponCard::where('coupon_id',$item->id)->where('is_void',1)->count();
            $item->overdue_count=CouponCard::where('coupon_id',$item->id)
                ->where('is_used',0)
                ->where('is_void',0)
                ->where('expired_at','<',date('Y-m-d H:i:s'))
                ->count();
            $item->total=CouponCard::where('coupon_id',$item->id)->count();
        }

        $coupon=$coupon->toArray();
        $response           = ReturnCode::success($coupon['data']);
        $response['_count'] = $coupon['total'];

        return response($response);
    }

    /**
     * 新增卡券
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/7/11 15:07
     */
    public function add(Request $request)
    {
        $user       = $request->user();
        $goods_id   = $request->input('goods_id',null);
        $expiredAt   = $request->input('expired_at',null);
        $name    = $request->input('name',null);

        try{
            $coupon=Coupon::where('name',$name)->first();
            if($coupon){
                return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'卡券名称已存在'));
            }

            $data=[
                'goods_id'   => $goods_id,
                'expired_at' => date('Y-m-d 23:59:59',$expiredAt),
                'name'       => $name,
            ];

            DB::beginTransaction();

            Coupon::create($data);

            DB::commit();

            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));

        }



    }

    /**
     * 编辑卡券
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/7/11 15:31
     */
    public function edit(Request $request,$id)
    {
        $coupon=Coupon::find($id);
        if(!$coupon){
            return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'为找到该卡券'));
        }

        $user       = $request->user();
        $goods_id   = $request->input('goods_id',null);
        $expiredAt   = $request->input('expired_at',null);
        $name    = $request->input('name',null);

        try{
            $coupon=Coupon::where('id','<>',$id)->where('name',$name)->first();
            if($coupon){
                return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'卡券名称已存在'));
            }

            DB::beginTransaction();

            Coupon::where('id',$id)->update([
                'goods_id'   => $goods_id,
                'expired_at' => date('Y-m-d 23:59:59',$expiredAt),
                'name'       => $name,
            ]);

            DB::commit();
            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }


    }

    /**
     * 删除设备
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @author yss
     * @date  2018/7/11 15:34
     */
    public function delete(Request $request,$id)
    {
        try{
            $coupon=Coupon::find($id);
            if(!$coupon){
                return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'未找到该卡券'));
            }

            $user=$request->user();

            $count=CouponCard::where('coupon_id',$id)->where('is_used',1)->count();
            if($count>0){
                return response(ReturnCode::error(ReturnCode::FAILED,'卡券已使用无法删除'));
            }

            DB::beginTransaction();

            CouponCard::where('coupon_id',$id)->delete();
            $coupon->delete();

            DB::commit();
            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }

    }

    /**
     * 导入卡券
     * @param Request $request
     * @author yss
     * @date  2018/11/27 15:38
     */
    public function importCard(Request $request,$id)
    {
        //判断请求中是否包含name=file的上传文件
        if(!$request->hasFile('file')){
            return response(ReturnCode::error(ReturnCode::FAILED,'上传文件为空'));
        }
        $file     = $request->file('file');
        //判断文件上传过程中是否出错
        if(!$file->isValid()){
            return response(ReturnCode::error(ReturnCode::FAILED,'文件上传出错'));
        }
        $fileSize = ceil($file->getClientSize() / 1024);
        $fileExt  = $file->getClientOriginalExtension();

        if ($fileExt) {
            $fileExt = strtolower($fileExt);
        }

        // 限制大小
        if ($fileSize > 4096) {
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,'文件超过限制大小4M'));
        }

        // 格式验证
        if ($fileExt !== 'xls' && $fileExt !== 'xlsx') {
            return response(ReturnCode::error(ReturnCode::FAILED,'文件格式错误'));
        }

        $path = 'uploads/' . date('Ymd') . '/';
        // 临时存储文件夹
        $storagePath = storage_path($path);
        // 创建ship_id目录
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777, true);
        }
        //重命名文件
        $fileName=date('YmdHis').str_random(5).'.'.$fileExt;
        // 转存图片
        $file->move($storagePath, $fileName);

        try{

            //读取excel数据
            $excelData=Excel::selectSheets('发给凯景')->load($storagePath.$fileName,function ($reader){
                return $reader->all();
            })->parsed;
            if(!$excelData){
                return response(ReturnCode::error(ReturnCode::FAILED,'数据为空，请重新选择文件'));
            }

            $card_sn=CouponCard::select(DB::raw('concat(sn,"_",pass_word)as xx'))->get();

            //循环拼合并字段数组
            foreach ($card_sn as $cItem)
            {
                $card_list[]=$cItem->xx;
            }

            $data=[];
            $sn='';
            $sns=[];

            foreach ($excelData as $item){

                $itemX=$item->sn.'_'.$item->pass_word;       //当前item字段拼接
                if (in_array($itemX,$card_list)){
                    $sn=$sn.$item->sn.'_'.$item->pass_word.',';     //因为松林的误操作，现将逻辑修改为卡券号+密码为唯一识别码，此处判断是否已存在
                }
                $itemM=$item->sn.$item->pass_word;
                $sns[]=$itemM;      //判断excel内卡券号+密码是否重复

                $data[]=[
                    'coupon_id'  => $id,
                    'sn'         => $item->sn,
                    'pass_word'  => $item->pass_word,
                    'enable_at'  => date('Y-m-d 0:00:00',strtotime($item->start_time)),
                    'expired_at' => date('Y-m-d 23:59:59',strtotime($item->end_time)),
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            $msg='';
            if($sn!=''){
                $msg='以下卡号+密码在系统中已存在： '.$sn.',';
            }
            if(count($sns) != count(array_unique($sns))){
                $msg=$msg.'excel中存在重复的卡号+密码，请检查';
            }
            if(!empty($msg)){
                return response(ReturnCode::error(ReturnCode::FAILED,$msg));

            }
            
            DB::beginTransaction();

            CouponCard::insert($data);

            DB::commit();
            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }
}