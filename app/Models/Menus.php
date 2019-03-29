<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/6/27
 * Time: 9:54
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menus extends Model
{
    use SoftDeletes;

    protected $table='menus';

    public $guarded = [];


    public function child()
    {
        return $this->hasMany(Menus::class,'parent_id');
    }


    public static function getMenu($parentId,$resource)
    {
        if(is_array($resource)){
            if(count($resource)<=0){
                return [];
            }
        }else{
            return [];
        }
        $data=self::where('parent_id',$parentId)
            ->where('is_show',1)
            ->select('id','byname','name','url','icon','parent_id')
            ->orderBy('sort','asc')
            ->with(['child'=>function ($q){
                $q->where('is_show',1)->select('id','byname','name','url','icon','parent_id')->orderBy('sort','asc');
            }])
            ->get();
        if(!$data){
            return null;
        }
        $menu=[];
        foreach ($data as $item){
            if(in_array($item->id,$resource)){
                $menu[$item->byname]=[
                    'name'=>$item->name,
                    'url'=>$item->url,
                    'byname'=>$item->byname,
                ];

                if($item->child){
                    foreach ($item->child as $child){
                        if(in_array($child->id,$resource)){
                            $menu[$item->byname]['child'][$child->byname]=[
                                'name'=>$child->name,
                                'url'=>$child->url,
                                'byname'=>$child->byname
                            ];
                        }
                    }
                }
            }

        }

        return $menu;

    }

    public static function getAllMenu($parentId)
    {
        $data=self::where('parent_id',$parentId)
            ->where('is_show',1)
            ->select('id','byname','name','url','icon','parent_id')
            ->orderBy('sort','asc')
            ->with(['child'=>function ($q){
                $q->where('is_show',1)->select('id','byname','name','url','icon','parent_id')->orderBy('sort','asc');
            }])
            ->get();
        if(!$data){
            return null;
        }
        $menu=[];
        foreach ($data as $item){
            $menu[$item->byname]=[
                'name'=>$item->name,
                'url'=>$item->url,
                'icon'=>$item->icon?$item->icon:"",
            ];

            if($item->child){
                foreach ($item->child as $child){
                    $menu[$item->byname]['child'][$child->byname]=[
                        'name'=>$child->name,
                        'url'=>$child->url,
                        'icon'=>$child->icon?$child->icon:""
                    ];
                }
            }
        }

        return $menu;
    }
}