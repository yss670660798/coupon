<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/7/11
 * Time: 14:27
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;
    protected $table='coupon';

    protected $guarded=[];

    public function good(){
        return $this->belongsTo(Goods::class,'goods_id');
    }

}