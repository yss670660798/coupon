<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/7/10
 * Time: 16:26
 */

namespace App\Http\Controllers\Admin;


class RoutesController extends Controller
{
    public function home()
    {
        $this->data['top_active'] = 'home';
        $this->data['sub_active'] = 'home';
        $this->data['js_file'] = 'home/index.js';
        return view('admin.home.index', $this->data);
    }

    public function goods()
    {
        $this->data['top_active'] = 'goods';
        $this->data['sub_active'] = 'goods';
        $this->data['js_file'] = 'goods/index.js';
        $this->data['url']=config('app.url');
        return view('admin.goods.index', $this->data);
    }

    public function sysUser()
    {
        $this->data['top_active'] = 'sys';
        $this->data['sub_active'] = 'sys_user';
        $this->data['js_file'] = 'sys/user.js';
        return view('admin.sys.user', $this->data);
    }

    public function sysRole()
    {
        $this->data['top_active'] = 'sys';
        $this->data['sub_active'] = 'sys_role';
        $this->data['js_file'] = 'sys/role.js';
        return view('admin.sys.role', $this->data);
    }

    public function sysMenu()
    {
        $this->data['top_active'] = 'admin_sys';
        $this->data['sub_active'] = 'admin_sys_menu';
        $this->data['js_file'] = 'sys/menu.js';
        return view('admin.sys.menu', $this->data);
    }

    public function sysLog()
    {
        $this->data['top_active'] = 'admin_sys';
        $this->data['sub_active'] = 'admin_sys_log';
        $this->data['js_file'] = 'sys/log.js';
        return view('admin.sys.log', $this->data);
    }

    public function coupon()
    {
        $this->data['top_active'] = 'coupon';
        $this->data['sub_active'] = 'coupon';
        $this->data['js_file'] = 'coupon/index.js';
        return view('admin.coupon.index', $this->data);
    }



    public function coupon_detail()
    {
        $this->data['top_active'] = 'coupon';
        $this->data['sub_active'] = 'coupon';
        $this->data['js_file'] = 'coupon/detail.js';
        return view('admin.coupon.detail', $this->data);
    }

    public function order()
    {
        $this->data['top_active'] = 'order';
        $this->data['sub_active'] = 'order';
        $this->data['js_file'] = 'order/index.js';
        return view('admin.order.index', $this->data);
    }

    public function activity()
    {
        $this->data['top_active'] = 'activity';
        $this->data['sub_active'] = 'activity';
        $this->data['js_file'] = 'activity/index.js';
        return view('admin.activity.index', $this->data);
    }

    public function activityAdd()
    {
        $this->data['top_active'] = 'activity';
        $this->data['sub_active'] = 'activity';
        $this->data['js_file'] = 'activity/add.js';
        return view('admin.activity.add', $this->data);
    }

    public function activityEdit()
    {
        $this->data['top_active'] = 'activity';
        $this->data['sub_active'] = 'activity';
        $this->data['js_file'] = 'activity/edit.js';
        return view('admin.activity.add', $this->data);
    }

}