<?php
/**
 * Created by PhpStorm.
 * User: hongpo
 * Date: 2018/9/13
 * Time: 16:13
 */

namespace App\Http\Controllers\Api;


use App\Libs\ReturnCode;
use App\Models\Menus;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword',10);
        $limit   = $request->input('limit',10);

        $menu=Menus::where('parent_id',$keyword)
            ->with(['child'=>function($c){
                $c->orderBy('sort','asc');
            }])
            ->orderBy('sort','asc')
            ->paginate($limit)
            ->toArray();

        $response           = ReturnCode::success($menu['data']);
        $response['_count'] = $menu['total'];

        return response($response);
    }

    /**
     * 添加
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author kevin
     * @date  2018-9-13 18:10:58
     */
    public function add(Request $request)
    {
        $menu_code = $request->input('menu_code',null);
        $menu_level= $request->input('menu_level',null);
        $name     = $request->input('name',null);
        $byname  = $request->input('byname',null);
        $parentId = $request->input('parent_id',0);
        $url      = $request->input('url',null);
        $sort    = $request->input('sort',999);
        $is_show    = $request->input('is_show',null);

        $menus=Menus::where('menu_code',$menu_code)->first();
        if($menus){
            return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'编号已存在'));
        }

        $menus=Menus::create([
            'menu_code'    => $menu_code,
            'menu_level'   => $menu_level,
            'name'    => $name,
            'byname'    => $byname,
            'parent_id'=> $parentId,
            'url' => $url,
            'sort'     => $sort,
            'is_show'   => $is_show==true?1:2,
        ]);

        return response(ReturnCode::success($menus));
    }

    /**
     * 编辑
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author kevin
     * @date  2018-9-13 18:10:47
     */
    public function edit(Request $request,$id)
    {
        $menus=Menus::find($id);
        if(!$menus){
            return response(ReturnCode::error(ReturnCode::NOT_FOUND,'未找到该记录'));
        }

        $menu_code = $request->input('menu_code',null);
        $menu_level= $request->input('menu_level',null);
        $name     = $request->input('name',null);
        $byname  = $request->input('byname',null);
        $parentId = $request->input('parent_id',0);
        $url      = $request->input('url',null);
        $sort    = $request->input('sort',999);
        $is_show    = $request->input('is_show',null);

        $menus=Menus::where('menu_code',$menu_code)->where('id','<>',$id)->first();
        if($menus){
            return response(ReturnCode::error(ReturnCode::RECORD_EXIST,'编号已存在'));
        }

        Menus::where('id',$id)->update([
            'menu_code'    => $menu_code,
            'menu_level'   => $menu_level,
            'name'    => $name,
            'byname'    => $byname,
            'parent_id'=> $parentId,
            'url' => $url,
            'sort'     => $sort,
            'is_show'   => $is_show==true?1:2,
        ]);

        return response(ReturnCode::success());
    }

    /**
     * @des 删除菜单
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function delete(Request $request,$id)
    {
        $menu=Menus::find($id);
        if(!$menu){
            return response(ReturnCode::error(ReturnCode::NOT_FOUND,'未找到该菜单'));
        }

        $menu->delete();

        return response(ReturnCode::success());
    }

    /**
     * 菜单列表--角色
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/12/13 21:42
     */
    public function menuList()
    {
        $menus=Menus::where('parent_id',0)
            ->where('is_show',1)
            ->select('id','byname','name','url','icon','parent_id')
            ->orderBy('sort','asc')
            ->with(['child'=>function ($q){
                $q->where('is_show',1)->select('id','byname','name','url','icon','parent_id')->orderBy('sort','asc');
            }])
            ->get();
        return response(ReturnCode::success($menus));
    }
}