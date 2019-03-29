<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/7/10
 * Time: 16:53
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goods extends Model
{
    use SoftDeletes;
    protected $table='goods';

    protected $guarded=[];

    public function img()
    {
        return $this->belongsTo(Attachment::class,'image_id');
    }

}