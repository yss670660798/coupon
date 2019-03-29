<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/6/27
 * Time: 10:00
 */

namespace App\Http\Controllers\Api;

use App\Libs\ReturnCode;
use App\Models\Menus;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class LoginController extends BaseController
{
    public $types=[
            '1'=>'admin',
            '2'=>'brand'
        ];
    public function login(Request $request)
    {
        $username = $request->input('username','yss');
        $password = $request->input('password','123456');

        if (Auth::attempt(['username'=>$username,'password'=>$password])) {
            $user=Auth::user();
            //$request->session()->put('token',$token);
            $url=$request->session()->get('url.intended',config('app.url').'home');

            $type=explode('/',parse_url($url)['path'])[1];
            if($this->types[$user->type]!=$type){
                $url=config('app.url').'home';
            }

            $request->session()->forget('url.intended');
            /*if(!$user->is_super){
                $role=UserRole::find($user->role_id);
                $menu=Menus::whereIn('id',$role->resource)->where('url',str_replace(config('app.url'),'',$url))->first();
                if(!$menu){
                    $user='/'.self::$types[$user->type].'/home';
                }
            }*/
            return response(['code' => ReturnCode::SUCCESS,'url'=>$url]);
        }
        return response(ReturnCode::create(ReturnCode::AUTHORIZE_FAIL));
    }

    public function fun($arr)
    {
        $len=count($arr);

        for($i=1;$i<$len;$i++){
            for($j=0;$j<($len-$i);$j++){
                if($arr[$i]>$arr[$j+1]){
                    $tmp=$arr[$i];
                    $arr[$j]=$arr[$j+1];
                    $arr[$j+1]=$tmp;
                }
            }
        }
    }

    /**
     * @des 修改密码
     * @author kevin
     * @date 2018-8-13 13:54:33
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function changePassword(Request $request)
    {
        try{
            $user     = $request->user();
            $oldPwd=$request->input('oldPwd','');
            $newPwd=$request->input('newPwd','');

            $uId=$user->id;
            $user=User::find($uId);

            if(!$user){
                return response(ReturnCode::error(8002,'未找到更新的记录'));
            }

            if(!password_verify($oldPwd, $user->password)){
                return response(ReturnCode::error(1007,'原密码错误'));
            }

            $pwd3=Hash::make($newPwd);
            $bool=User::where('id',$uId)->update(['password'=>$pwd3]);

            if(!$bool){
                return response(ReturnCode::error(8009,'密码修改失败'));
            }

            return response(ReturnCode::success([],'修改成功'));

        }catch (\Exception $exception){
            Log::error($exception);
            return response(ReturnCode::error(1007,$exception->getMessage()));
        }

    }
}