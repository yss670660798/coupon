<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/12/13
 * Time: 21:14
 */

namespace App\Http\Controllers\Api;


use App\Libs\ReturnCode;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserRoleController extends Controller
{
    /**
     * 角色列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/13 21:18
     */
    public function index(Request $request)
    {
        $limit   = $request->input('limit',10);
        $keyword = $request->input('keyword',null);

        $where=function ($q)use($keyword){
            if(!empty($keyword)){
                $q->where('name','like','%'.$keyword.'%');
            }
        };

        try{
            $roles=UserRole::where($where)
                ->select(['id','name','resource','note','created_at'])
                ->paginate($limit)
                ->toArray();

            $response           = ReturnCode::success($roles['data']);
            $response['_count'] = $roles['total'];

            return response($response);
        }catch (\Exception $e){
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }
    }

    /**
     * 新建角色
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/13 22:06
     */
    public function add(Request $request)
    {
        $name=$request->input('name',null);

        if(empty($name)){
            return response(ReturnCode::error(ReturnCode::FAILED,'角色名不能为空'));
        }

        $role=UserRole::where('name',$name)->first();

        if($role){
            return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'该角色已存在'));
        }

        try{
            DB::beginTransaction();

            UserRole::create([
                'name'=>$name,
                'note'=>$request->input('note'),
                'resource'=>$request->input('resource'),
                'half_resource'=>$request->input('half_resource')
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
     * 修改角色
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/13 22:05
     */
    public function edit(Request $request,$id)
    {
        $role=UserRole::find($id);

        if(!$role){
            return response(ReturnCode::error(ReturnCode::NOT_FOUND,'未找到该角色'));
        }
        $name=$request->input('name',null);
        if(empty($name)){
            return response(ReturnCode::error(ReturnCode::FAILED,'角色名不能为空'));
        }

        $role2=UserRole::where('name',$name)->where('id','<>',$id)->first();

        if($role2){
            return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'该角色已存在'));
        }

        try{
            DB::beginTransaction();

            $role->name=$name;
            $role->note=$request->input('note');
            $role->resource=$request->input('resource');
            $role->half_resource=$request->input('half_resource');

            $role->save();
//            UserRole::where('id',$id)
//                ->update([
//                    'name'=>$name,
//                    'note'=>$request->input('note',null),
//                    'resource'=>$request->input('resource',[]),
//                    'half_resource'=>$request->input('half_resource',[])
//                ]);

            DB::commit();
            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }

    }

    /**
     * 删除角色
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/13 22:02
     */
    public function delete(Request $request,$id)
    {
        $role=UserRole::find($id);
        if(!$role){
            return response(ReturnCode::error(ReturnCode::NOT_FOUND,'未找到该角色'));
        }

        $user=User::where('role_id',$id)->first();
        if($user){
            return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'该角色已被使用无法删除'));
        }

        try{
            DB::beginTransaction();
            $role->delete();
            DB::commit();
            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage()));
        }




    }

    /**
     * 权限列表--下拉
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/14 16:22
     */
    public function getList(Request $request)
    {
        $data=UserRole::get(['id','name']);

        return response(ReturnCode::success($data));
    }
}