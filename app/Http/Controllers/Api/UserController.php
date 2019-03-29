<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/7/10
 * Time: 18:16
 */

namespace App\Http\Controllers\Api;


use App\Libs\ReturnCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * 用户列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date   2018/7/10 18:20
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword',null);
        $limit   = $request->input('limit',10);

        $where=function ($q)use($keyword){
            if(!empty($keyword)){
                $q->where(function ($qq)use ($keyword){
                    $qq->orWhere('name','like','%'.$keyword.'%');
                    $qq->orWhere('username','like','%'.$keyword.'%');
                    $qq->orWhere('email','like','%'.$keyword.'%');
                    $qq->orWhere('tel','like','%'.$keyword.'%');
                });
            }
        };

        $order=User::where($where)
            ->select('id','type',
                'name','username','role_id','email','tel','status','login_at','login_ip','created_at')
            ->with(['role'=>function($r){
                $r->select('id','name');
            }])
            ->paginate($limit)
            ->toArray();

        $response           = ReturnCode::success($order['data']);
        $response['_count'] = $order['total'];

        return response($response);
    }

    /**
     * 添加品牌
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date   2018/7/10 18:26
     */
    public function add(Request $request)
    {
        $username = $request->input('username',null);
        $name     = $request->input('name',null);
        $tel      = $request->input('tel',null);
        $email    = $request->input('email',null);
        $role_id  = $request->input('role_id',null);


        $adminUser=User::where('username',$username)->first();
        if($adminUser){
            return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'用户名已存在'));
        }

        try{
            DB::beginTransaction();
            User::create([
                'type'     => User::TYPE_ADMIN,
                'name'     => $name,
                'username' => $username,
                'password' => Hash::make('123456'),
                'tel'      => $tel,
                'email'    => $email,
                'role_id'  => $role_id
            ]);
            DB::commit();
            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage());
        }

    }

    /**
     * 编辑用户
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date   2018/7/10 18:29
     */
    public function edit(Request $request,$id)
    {
        $user=User::find($id);
        if(!$user){
            return response(ReturnCode::error(ReturnCode::NOT_FOUND,'未找到该用户'));
        }

        $name     = $request->input('name',null);
        $tel      = $request->input('tel',null);
        $email    = $request->input('email',null);
        $role_id  = $request->input('role_id',null);

        $user=User::where('name',$name)->where('id','<>',$id)->first();
        if($user){
            return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'用户名已存在'));
        }

        try{
            DB::beginTransaction();
            User::where('id',$id)->update([
                'name'      => $name,
                'tel'       => $tel,
                'email'     => $email,
                'role_id'   =>$role_id
            ]);
            DB::commit();
            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return ReturnCode::error(ReturnCode::SYSTEM_FAIL,$e->getMessage());
        }

    }

    /**
     * 删除用户
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @author yss
     * @date  2018/7/10 17:01
     */
    public function delete(Request $request,$id)
    {
        $user=User::find($id);
        if(!$user){
            return response(ReturnCode::error(ReturnCode::NOT_FOUND,'未找到该用户'));
        }

        try{
            DB::beginTransaction();
            $user->delete();
            DB::commit();
            return response(ReturnCode::success());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return response(ReturnCode::error(1006,$e->getMessage()));
        }

    }

    /**
     * @des 重置密码
     * @author kevin
     * @date 2018-9-13 15:34:42
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resetPwd(Request $request,$id)
    {

        $user=User::find($id);
        if(!$user){
            return response(ReturnCode::error(ReturnCode::NOT_FOUND,'未找到该用户'));
        }

        try{
            DB::beginTransaction();
            $user->password=Hash::make('123456');
            $user->save();
            DB::commit();
            return response(ReturnCode::success([],'重置密码成功【123456】'));
        }
        catch (\Exception $exception){
            DB::rollBack();
            Log::error($exception);
            return response(ReturnCode::error(1006,$exception->getMessage()));
        }
    }
}