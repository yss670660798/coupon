<?php
/**
 * Created by PhpStorm.
 * User: hongpo
 * Date: 2018/8/28
 * Time: 13:18
 */

namespace App\Http\Controllers\Api;

use App\Libs\ReturnCode;
use App\Models\ApiLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * @des 日志查询
     * @author kevin
     * @date 2018-8-28 13:48:00
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword',null);
        $date = $request->input('date',null);
        $limit   = $request->input('limit',10);

        $where=function ($q)use($keyword,$date){
            if(!empty($date)){
                $q->whereRaw('DATE_FORMAT(created_at,\'%Y-%m-%d\')=?',$date);
            }else{
                $q->whereRaw('DATE_FORMAT(created_at,\'%Y-%m-%d\')=?',date('Y-m-d',time()));
            }
            if(!empty($keyword)){
                $q->where(function ($qq)use ($keyword){
//                    $qq->orWhere('name','like','%'.$keyword.'%');
                });
            }
        };

        $order=ApiLog::where($where)
            ->with(['user'=>function($u){
                $u->select('id','name');
            }])
            ->paginate($limit)
            ->toArray();

        $response           = ReturnCode::success($order['data']);
        $response['_count'] = $order['total'];

        return response($response);
    }

    /**
     * @des 日志删除
     * @author kevin
     * @date 2018-8-28 13:48:00
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function delete(Request $request,$id)
    {
        $log=ApiLog::find($id);
        if(!$log){
            return response(ReturnCode::error(ReturnCode::NOT_FOUND,'未找到记录'));
        }

        $log->delete();
        return response(ReturnCode::success());
    }

}