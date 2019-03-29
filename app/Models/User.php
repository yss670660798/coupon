<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/7/10
 * Time: 16:08
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;
    protected $table='user';

    public $guarded = [];

    const TYPE_ADMIN=1;
    const TYPE_BRAND=2;

    public function brand()
    {
        return $this->hasOne(Goods::Class,'id','brand_id');
    }

    public function role()
    {
        return $this->belongsTo(UserRole::Class);
    }
}