<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/1/22
 * Time: 16:46
 */

namespace App\Libs;


use Illuminate\Support\Facades\Log;
use JMessage\IM\Group;
use JMessage\IM\Message;
use JMessage\IM\User;
use JMessage\JMessage;

class appJMessage
{
    protected static $appKey = '131e89023aa24f335c8bae1f';
    protected static $secret = '3e5c32aced4eaefcf624e02e';

    protected static function client(){
        $client = new JMessage(self::$appKey,self::$secret,[ 'disable_ssl' => true ]);

        return $client;
    }

    //创建用户实体
    protected static function user(){
       return new User(self::client());
    }

    //文本消息实体
    protected static function message(){
        return new Message(self::client());
    }
    
    //群组实体
    protected static function group(){
        return new Group(self::client());
    }

    //用户注册
    public static function register($username,$password)
    {
        $response=self::user()->register($username, $password);
        return self::response($response);
    }
    //获得用户信息
    public static function show($username)
    {
        $response=self::user()->show($username);
        return self::response($response);
    }
    //更新用户信息
    public static function update($username,array $options)
    {
        $response=self::user()->update($username, $options);
        return self::response($response);
    }
    //获得用户列表
    public static function listAll($count, $start = 0)
    {
        $response=self::user()->listAll($count, $start);
        return self::response($response);
    }

    //修改密码
    public static function updatePassword($username,$password)
    {
        $response=self::user()->updatePassword($username, $password);
        return self::response($response);
    }

    //批量注册 最多支持500
    public static function batchRegister($users)
    {
        $response=self::user()->batchRegister($users);
        return self::response($response);
    }
    
    //查询用户在线状态
    public static function stat($username)
    {
        $response=self::user()->stat($username);
        return self::response($response);
    }

    //删除用户
    public static function delete($username)
    {
        $response=self::user()->delete($username);
        return self::response($response);
    }
    
    //发送文本消息
    public static function sendText(  $target,  $msg,  $notification = [],  $options = [])
    {
        $response=self::message()->sendText(1,  ['type'=>'admin','id'=>'admin'],  ['type'=>'single','id'=>$target],  ['text'=>$msg],  $notification , $options );
        return self::response($response);
    }
    
    /*
     * 创建群组
     * $owner 群主用户名
     * $name  群名称
     * $desc  群描述
     * $members  群成员
     * */
    public static function GroupCreate($owner, $name, $desc, $members = [])
    {
        $response=self::group()->create($owner,$name,$desc,$members);
        return self::response($response);
    }

    /*
     * 获取群组详情
     * $gid: 群组 ID, 由创建群组时分配
     * */
    public static function GroupShow($gid)
    {
        $response = self::group()->show($gid);
        return self::response($response);
    }

    /*
     * 更新群组信息（群名 or 群描述）
     * $gid: 群组 ID, 由创建群组时分配
     * $name: 新的群名
     * $desc: 新的群描述
     * */
    public static function GroupUpdate($gid,$name=null,$desc=null)
    {
        $response = self::group()->update($gid,$name,$desc);
        return self::response($response);
    }

    /*
     * 删除群组
     * $gid: 群组 ID, 由创建群组时分配
     * */
    public static function GroupDelete($gid)
    {
        $response = self::group()->delete($gid);
        return self::response($response);
    }

    /*
     * 添加群组成员
     * $gid: 群组 ID, 由创建群组时分配
     * $usernames: 表示要添加到群组的用户数组
     * */
    public static function GroupAddMembers($gid,$usernames)
    {
        $response = self::group()->addMembers($gid,  $usernames);
        return self::response($response);
    }

    /*
     * 移除群组成员
     * $gid: 群组 ID, 由创建群组时分配
     * $usernames: 表示要添加到群组的用户数组
     * */
    public static function GroupRemoveMembers($gid,$usernames)
    {
        $response = self::group()->removeMembers($gid,  $usernames);
        return self::response($response);
    }

    /*
     * 更新群组成员  建议使用上面所述的 2 个方法分别添加和移除群组成员
     * $gid: 群组 ID, 由创建群组时分配
     * $add: 添加到群组的用户数组
     * $remove:从群组移除的用户数组
     * */
    public static function GroupUpdateMembers($gid,$add,$remove)
    {
        $response = self::group()->updateMembers($gid, [ 'add' => $add, 'remove' => $remove ]);
        return self::response($response);
    }

    /*
     * 获取群组成员列表
     * $gid: 群组 ID, 由创建群组时分配
     * */
    public static function GroupMembers($gid)
    {
        $response=self::group()->members($gid);
        return self::response($response);
    }

    /*
     * 获取当前应用的群组列表
     * $start: 开始的记录数
     * $count: 本次读取的记录数量，最大值为500
     * */
    public static function GroupListAll($count, $start = 0)
    {
        $count=$count>500?500:$count;
        $response = self::group()->listAll($count, $start);
        return self::response($response);
    }


    protected static function response($response){
        if(isset($response['body']['error'])){
            Log::info(json_encode($response['body']).'---'.$response['http_code']);  //存在错误时记录日志
        }
        return $response;
    }
}