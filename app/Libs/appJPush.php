<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/3/1
 * Time: 17:45
 */

namespace App\Libs;


class appJPush
{
    protected static $appKey = '131e89023aa24f335c8bae1f';
    protected static $secret = '3e5c32aced4eaefcf624e02e';

    /**
     * @param $msg 消息内容
     * @param $clientId 设备id
     * @author yss
     * @date  2018/3/1 18:12
     */
    public static function jPush($msg,$clientId){
        //$client = new JPush(self::$appKey, self::$secret, storage_path('logs/jpush.log'));
        $client=new \JPush(self::$appKey,self::$secret,storage_path('logs/jpush.log'));

        $client->push()
            ->setPlatform('all')
            ->addAlias($clientId)
            ->setMessage($msg)
            ->send();
    }
}