<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2019/1/12
 * Time: 16:28
 */

namespace App\Http\Controllers\Api;


use App\Libs\ReturnCode;
use App\Models\ActivityConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
{
    /**
     * 提领列表
     * @param Request $request
     * @return mixed
     * @author yss
     * @date  2019/1/12 16:36
     */
    public function index(Request $request)
    {
        $limit      = $request->input('limit',10);
        $activeDate = $request->input('date',null);
        $keyword    = $request->input('keyword',null);

        try{
            $where=function ($q)use($activeDate,$keyword){
                if(!empty($keyword)){
                    $q->where('name','like','%'.$keyword.'%');
                }

                //if(!empty($activeDate)){
                //    $q->
                //}
            };

            $nowTime=time();
            $activitys=ActivityConfig::where($where)
                ->paginate($limit);
            foreach ($activitys as $activity){
                $activity->is_active=0;
                if(strtotime($activity->start_time)<$nowTime && strtotime($activity->end_time)){
                    $activity->is_active=1;
                }
            }

            $activitys=$activitys->toArray();

            $response['code']=ReturnCode::SUCCESS;
            $response['data']=$activitys['data'];
            $response['_count']=$activitys['total'];

            return response($response);
        }catch (\Exception $e){
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }

    }

    /**
     * 创建提领
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2019/1/12 16:49
     */
    public function create(Request $request)
    {
        $name       = $request->input('name',null);
        $date       = $request->input('date',[]);
        $notice_img = $request->input('notice_img',0);
        $title_img  = $request->input('title_img',0);
        $end_img    = $request->input('end_img',0);

        try{

            $activity=ActivityConfig::where('name',$name)->first();
            if($activity){
                return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'提领名称已存在'));
            }

            $activity=ActivityConfig::where('status',1)
                ->where('end_time','>=',date('Y-m-d 23:59:59',strtotime($date[0])))
                ->first();
            if($activity){
                return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'提领时间交叉'));
            }

            DB::beginTransaction();

            ActivityConfig::create([
                'name'       => $name,
                'start_time' => date('Y-m-d 0:00:00',strtotime($date[0])),
                'end_time'   => date('Y-m-d 23:59:59',strtotime($date[1])),
                'notice_img' => $notice_img,
                'title_img'  => $title_img,
                'end_img'    => $end_img,
            ]);

            DB::commit();

            return response(ReturnCode::success());
        }catch (\Exception $e){
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }

    /**
     * 更改提领状态
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2019/1/14 18:14
     */
    public function changeStatus(Request $request,$id)
    {
        try{
            $activity=ActivityConfig::find($id);
            if(!$activity){
                return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'提领无效'));
            }

            $status=$request->input('status',2);

            if($status==1){
                $activitied=ActivityConfig::where('status',1)
                    ->where('end_time','>=',$activity->start_time)
                    ->first();

                if($activitied){
                    return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'与正在进行的提领活动交叉，无法启用'));
                }
            }
            $activity->status=$status;
            $activity->save();

            return response(ReturnCode::success());
        }catch (\Exception $e){
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }

    /**
     * 提领信息
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2019/1/14 20:49
     */
    public function info(Request $request,$id)
    {
        try{
            $activity=ActivityConfig::with(['notice','title','end'])->find($id);
            if(!$activity){
                return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'提领无效'));
            }
            $nowTime=time();
            $activity->is_active=false;
            if(strtotime($activity->start_time)<$nowTime && strtotime($activity->end_time) && $activity->status==1){
                $activity->is_active=1;
            }

            return response(ReturnCode::success($activity));
        }catch (\Exception $e){
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }

    /**
     * 修改提领
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2019/1/14 21:15
     */
    public function update(Request $request,$id)
    {
        try{
            $activity=ActivityConfig::find($id);
            if(!$activity){
                return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'提领无效'));
            }

            $name       = $request->input('name',null);
            $date       = $request->input('date',[]);
            $notice_img = $request->input('notice_img',0);
            $title_img  = $request->input('title_img',0);
            $end_img    = $request->input('end_img',0);

            DB::beginTransaction();

            $activity->name       = $name;
            $activity->notice_img = $notice_img;
            $activity->title_img  = $title_img;
            $activity->end_img    = $end_img;
            if($activity->status!=1){
                $activity->start_time = date('Y-m-d 0:00:00',strtotime($date[0]));
                $activity->end_time   = date('Y-m-d 23:59:59',strtotime($date[1]));
            }

            $activity->save();

            DB::commit();
            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }

    /**
     * 删除提领
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2019/1/14 18:25
     */
    public function delete(Request $request,$id)
    {
        try{
            $activity=ActivityConfig::find($id);
            if(!$activity){
                return response(ReturnCode::error(ReturnCode::RECORD_NOT_EXIST,'提领无效'));
            }

            $activity->delete();

            return response(ReturnCode::success());
        }catch (\Exception $e){
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }
}