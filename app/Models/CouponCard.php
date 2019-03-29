<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/11/27
 * Time: 14:20
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponCard extends Model
{
    use SoftDeletes;
    protected $table='coupon_card';

    protected $guarded=[];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}