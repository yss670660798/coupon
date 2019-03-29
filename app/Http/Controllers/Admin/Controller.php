<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/7/10
 * Time: 16:24
 */

namespace App\Http\Controllers\Admin;

use App\Models\Menus;
use App\Models\UserRole;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    protected $data=[];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            $role=UserRole::find($user->role_id);

            //$menu = $user->is_super?Menus::getAllMenu(10):Menus::getMenu(10, $role->resource);
            $menu = Menus::getMenu(0, $role->resource, 1);

            $this->data['user']=$user;
            $this->data['menu'] = $menu;

            return $next($request);
        });
    }
}