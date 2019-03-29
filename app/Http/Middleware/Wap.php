<?php

namespace App\Http\Middleware;

use App\Libs\CheckBrowser;
use Closure;
use EasyWeChat\Foundation\Application;

class Wap
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $config=null;
        $browser=true;
        //微信环境
        if(CheckBrowser::isWx()){

            $config=config('wechat');
            $app=new Application($config);
            $oauth=$app->oauth;

            //获得微信用户信息
            $user=$request->session()->get('wechat_user');
            //判断是否授权
            if(!$user){
                return $oauth->redirect();
            }

        }

        //支付宝环境
        if(CheckBrowser::isAlipay()){

        }

        //不在指定浏览器
        if($browser){

        }

        $request->session()->put('member',$user);
        return $next($request);
    }
}
