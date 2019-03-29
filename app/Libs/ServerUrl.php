<?php
/**
 * Created by PhpStorm.
 * User: hongpo
 * Date: 2018/8/9
 * Time: 17:27
 */

namespace App\Libs;


class ServerUrl
{
    //1.Jerry 接口
    //训练请求
    const API_URL_POST_TRAIN='train';

    //查看进度条
    const API_URL_GET_PROGRESS='get_model_processing';

    //查看日志
    const API_URL_GET_LOG='get_log';

    //强制结束
    const API_URL_POST_STOP='finish_train';



}