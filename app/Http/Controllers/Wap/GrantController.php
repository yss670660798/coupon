<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/6/27
 * Time: 14:32
 */

namespace App\Http\Controllers\Wap;

use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
class GrantController extends BaseController
{
    public function index(Request $request)
    {
        $config=config('wechat');
        $app=new Application($config);
        $oauth = $app->oauth;

        $user=$oauth->user();

        $request->session()->put('wechat_user',$user->toArray());

        $targetUrl = empty($request-session()->get('target_url')) ? '/' : $request-session()->get('target_url');

        return redirect($targetUrl);
    }
}