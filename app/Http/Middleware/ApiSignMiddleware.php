<?php

namespace App\Http\Middleware;

use App\Libs\ReturnCode;
use App\Libs\Sign;
use Closure;
use Illuminate\Support\Facades\Log;

class ApiSignMiddleware
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
        $appId=$request->input('app_id',false);
        $timestamp=$request->input('timestamp',false);
        $nonce=$request->input('nonce',false);
//        Log::info($appId);

        if(!$appId){
            return response(ReturnCode::error(1001,'[app_id]错误'));
        }

        if(!$timestamp){
            return response(ReturnCode::error(1003,'[timestamp]错误'));
        }

        if(!$this->checkTimestamp($timestamp)){
//            return response(ReturnCode::error(1003,'[timestamp]超出时间界限'));
        }

        if(!$nonce){
            return response(ReturnCode::error(1004,'[nonce]错误'));
        }

        $data=Sign::where('status',1)->where('app_id',$appId)->first();
        if(!$data){
            return response(ReturnCode::error(1002,'[app_id]不存在'));
        }
        $secret=$data->app_secret;

        if(!$this->checkAppId($appId,$timestamp,$nonce,$secret)){
            return response(ReturnCode::error(1006,'签名错误'));
        }

        return $next($request);
    }
    private function checkAppId($appId,$timestamp,$nonce,$secret)
    {
        try{
            $checkMd5=strtoupper(md5($appId.$timestamp.$secret));
            if($nonce!==$checkMd5){
                return false;
            }
            return true;
        }
        catch (\Exception $exception){
            Log::error($exception);
            return false;
        }
    }

    //判断时间戳是否超时
    private function checkTimestamp($timestamp){
        $timeDiff=$this->timeDiff($timestamp,time());
//        Log::info(time());
//        Log::info($timeDiff);
        if(abs($timeDiff)<=2){
            return true;
        }

        return false;
    }

    /**
     * 计算两个时间戳之差
     * @param $begin_time
     * @param $end_time
     * @return array
     */
    private function timeDiff( $begin_time, $end_time ){
        if ( $begin_time < $end_time ) {
            $startTime = $begin_time;
            $endTime = $end_time;
        } else {
            $startTime = $end_time;
            $endTime = $begin_time;
        }
        $timeDiff = $endTime - $startTime;
        $days = intval( $timeDiff / 86400 );
        $remain = $timeDiff % 86400;
        $hours = intval( $remain / 3600 );
        $remain = $remain % 3600;
        $mins = intval( $remain / 60 );
        $secs = $remain % 60;
        $res = array( "day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs );
//        Log::info($res);
        return $mins;
    }
}
