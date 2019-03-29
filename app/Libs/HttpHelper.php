<?php
/**
 * Created by PhpStorm.
 * User: hongpo
 * Date: 2018/7/24
 * Time: 13:34
 */

namespace App\Libs;

use Illuminate\Support\Facades\Log;

class HttpHelper
{
    //curl get
    public static function curlGet($url)
    {
        if (empty($url)) {
            return '';
        }
        $url_ch = curl_init();
        curl_setopt($url_ch, CURLOPT_URL, $url);
        //curl_setopt($url_ch, CURLOPT_USERAGENT, kr_randUseragent());
        curl_setopt($url_ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($url_ch, CURLOPT_FOLLOWLOCATION, 1); //是否抓取跳转后的页面

        curl_setopt($url_ch, CURLOPT_REFERER, $url);

        curl_setopt($url_ch, CURLOPT_TIMEOUT, 10);
        $url_output = trim(curl_exec($url_ch));
        curl_close($url_ch);
        if ($url_output) {
            return $url_output;
        }
        return '';
    }
    //curl post
    public static function curlPost($url,$data){
        $data  = json_encode($data);
        $headerArray =array("Content-type:application/json;charset='utf-8'","Accept:application/json");
//        $headerArray =array("Content-type:multipart/form-data;charset='utf-8'","Accept:application/form-data");
//        $headerArray =array("Content-type:application/x-www-form-urlencoded;charset='utf-8'");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headerArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
//        return $output;
        return json_decode($output,true);
    }
}