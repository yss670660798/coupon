<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/7/16
 * Time: 13:32
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes;
    protected $table='attachment';

    protected $guarded=[];
}