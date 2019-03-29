<?php
/**
 * Created by PhpStorm.
 * User: hongpo
 * Date: 2018/7/27
 * Time: 11:56
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppWF extends Model
{
    use SoftDeletes;

    protected $table='apps_wf';

    protected $guarded=[];

    protected $dates= ['deleted_at'];
}