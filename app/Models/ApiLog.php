<?php
/**
 * Created by PhpStorm.
 * User: hongpo
 * Date: 2018/8/27
 * Time: 18:06
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiLog extends Model
{
    use  SoftDeletes;

    protected $table='log';

    protected  $guarded=[];

    public $casts = [
        'params' => 'json'
    ];

    public function user()
    {
        return $this->hasOne(User::Class,'id','user_id');
    }

    public function menu()
    {
        return $this->hasOne(Menu::Class,'url','path');
    }
}