<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2019/1/9
 * Time: 21:14
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityConfig extends Model
{
    use SoftDeletes;
    protected $table='activity_config';

    protected $guarded=[];

    public function notice()
    {
        return $this->belongsTo(Attachment::class,'notice_img');
    }

    public function title()
    {
        return $this->belongsTo(Attachment::class,'title_img');
    }

    public function end()
    {
        return $this->belongsTo(Attachment::class,'end_img');
    }
}