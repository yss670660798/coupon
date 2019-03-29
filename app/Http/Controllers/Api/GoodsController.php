<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/7/10
 * Time: 16:50
 */

namespace App\Http\Controllers\Api;


use App\Libs\ReturnCode;
use App\Models\Goods;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GoodsController extends Controller
{
    /**
     * 商品列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/7/10 16:54
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword',null);
        $limit   = $request->input('limit',10);

        $where=function ($q)use($keyword){
            if(!empty($keyword)){
                $q->where(function ($qq)use ($keyword){
                    $qq->orWhere('name','like','%'.$keyword.'%');
                    $qq->orWhere('sn','like','%'.$keyword.'%');
                });
            }
        };

        $brand=Goods::where($where)
            ->orderByDesc('id')
            ->with(['img'=>function($q){
                $q->select(['id','file_name','file_url']);
            }])
            ->paginate($limit)
            ->toArray();

        $response           = ReturnCode::success($brand['data']);
        $response['_count'] = $brand['total'];

        return response($response);
    }

    /**
     * 添加商品
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/7/10 16:57
     */
    public function add(Request $request)
    {
        try{
            $sn      = $request->input('sn',null);
            $name    = $request->input('name',null);
            $imageId = $request->input('image_id',0);
            $price   = $request->input('price',0);
            $desc    = $request->input('desc',null);


            $good=Goods::where('sn',$sn)->first();
            if($good){
                return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'商品编号已存在'));
            }

            $good=Goods::where('name',$name)->first();
            if($good){
                return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'商品名称已存在'));
            }

            DB::beginTransaction();
            Goods::create([
                'sn'       => $sn,
                'name'     => $name,
                'image_id' => $imageId,
                'price'    => $price,
                'intro'    => $desc,
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
     * 编辑商品
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/7/10 17:00
     */
    public function edit(Request $request,$id)
    {
        try{
            $good=Goods::find($id);
            if(!$good){
                return response(ReturnCode::error(ReturnCode::NOT_FOUND,'未找到该商品'));
            }

            $sn      = $request->input('sn',null);
            $name    = $request->input('name',null);
            $imageId = $request->input('image_id',0);
            $price   = $request->input('price',0);
            $desc    = $request->input('desc',null);

            $good=Goods::where('sn',$sn)->where('id','<>',$id)->first();
            if($good){
                return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'商品编号已存在'));
            }

            $good=Goods::where('name',$name)->where('id','<>',$id)->first();
            if($good){
                return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'商品名称已存在'));
            }

            DB::beginTransaction();
            Goods::where('id',$id)->update([
                'sn'        => $sn,
                'name'      => $name,
                'price'     => $price,
                'image_id'  => $imageId,
                'intro   '  => $desc,
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
     * 添加库存
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2019/2/7 10:23
     */
    public function addStock(Request $request,$id)
    {
        try{
            $goods=Goods::find($id);
            if(!$goods){
                return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'商品不存在'));
            }

            $stock=$request->input('add_stock',1);

            DB::beginTransaction();

            $goods->stock+=$stock;
            $goods->save();

            DB::commit();

            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }

    /**
     * 删除商品
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @author yss
     * @date  2018/7/10 17:01
     */
    public function delete(Request $request,$id)
    {
        $good=Goods::find($id);
        if(!$good){
            return response(ReturnCode::error(ReturnCode::NOT_FOUND,'未找到该商品'));
        }

        $good->delete();

        return response(ReturnCode::success());
    }

    /**
     * 产品下拉表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/11/27 14:04
     */
    public function getList(Request $request)
    {
        $goods=Goods::get(['id','name']);

        return response(ReturnCode::success($goods));
    }

}