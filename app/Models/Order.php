<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/12/14
 * Time: 18:03
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table='order';

    protected $guarded=[];

    public function couponCard()
    {
        return $this->belongsTo(CouponCard::class);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
}