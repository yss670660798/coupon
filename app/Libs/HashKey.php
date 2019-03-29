<?php
/**
 * Created by PhpStorm.
 * User: hongpo
 * Date: 2018/8/13
 * Time: 14:35
 */

namespace App\Libs;


class HashKey
{
    //https://www.cnblogs.com/dcb3688/p/4608007.html



    /**
     * @des 随机生成cd_key ,20个字符
     * @param $len - 分隔字符串个数
     * @param int $step 单个字符个数
     * @return string
     */
    public static function cdKey($len,$step=5)
    {
        $res=[];
        for($l=1;$l<=$len;$l++){
            $str='';
            for($i=1;$i<=$step;$i++){
                $str=$str.chr(rand(65, 90));
            }
            $res[]=$str;
        }
        $return=implode('-',$res);
        return $return;
    }


    /**
     * @des 字符串
     * @date 2018-8-16 17:03:44
     * @param $len
     * @param int $step
     * @param string $key
     * @return string
     */
    public static function strKey($len,$step=5,$key='')
    {
        $res=[];
        for($l=1;$l<=$len;$l++){
            $str='';
            for($i=1;$i<=$step;$i++){
                $str=$str.chr(rand(65, 90));
            }
            $res[]=$str;
        }
        $return=implode($key,$res);
        return $return;
    }
}