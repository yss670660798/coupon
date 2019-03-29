<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/7/10
 * Time: 16:09
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use SoftDeletes;
    protected $table='user_role';

    protected $guarded=[];

    public $casts = [
        'resource' => 'json',
        'half_resource' => 'json',
    ];
}