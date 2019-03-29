<?php
/**
 * Created by PhpStorm.
 * User: hongpo
 * Date: 2018/7/20
 * Time: 17:10
 */

namespace App\Libs;


use Illuminate\Database\Eloquent\Model;

class Sign extends Model
{
    protected $table = 'attach_apps';

    protected $fillable = [
        'id', 'app_id', 'app_secret', 'app_name', 'app_desc', 'status', 'created_at', 'updated_at'
    ];
}