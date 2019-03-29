<?php

namespace App\Http\Middleware;

use App\Libs\ReturnCode;
use Closure;
use Illuminate\Support\Facades\Auth;

class isLogin
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
        //判断是否登录
        if(!Auth::check()){
            return ReturnCode::error(ReturnCode::AUTHORIZE_FAIL,'没有登录或登录过期，请重新登录');
        }
        return $next($request);
    }
}
