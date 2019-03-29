<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/6/27
 * Time: 14:08
 */

namespace App\Libs;


class CheckBrowser
{
    /**
     * 判断微信环境
     * @return bool
     * @author yss
     * @date  2018/6/27 14:08
     */
    public static function isWx()
    {
        if (strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger")) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断支付宝环境
     * @return bool
     * @author yss
     * @date  2018/6/27 14:09
     */
    public static function isAlipay()
    {

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient')) {
            return true;
        }else{
            return false;
        }
    }
}