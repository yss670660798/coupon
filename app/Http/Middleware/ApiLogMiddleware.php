<?php
/**
 * Created by PhpStorm.
 * User: hongpo
 * Date: 2018/4/13
 * Time: 13:58
 */

namespace App\Http\Middleware;

//use App\Libs\RouterCode;
use App\Models\ApiLog;
use Closure;
use Illuminate\Support\Facades\Log;

class ApiLogMiddleware
{
    public function handle($request, Closure $next)
    {

        $method = $request->method();
        $path=$request->path();

//        if ($method == 'GET') {
//            return $next($request);
//        }
//        if(strpos($path,'base')||($method == 'GET'&&strpos($path,'log'))){
//            return $next($request);
//        }
        $log = [
            'method' => $method,
            'path'=>$path,
//            'fun'=>RouterCode::$routerFun[$path],
            'url' => $request->url(),
            'params' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->server()['HTTP_USER_AGENT'],
            'type' => 0,
            'user_id' => $request->user()?$request->user()->id:0
        ];

        ApiLog::create($log);

        return $next($request);
    }

}