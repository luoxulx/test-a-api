<?php
/**
 * Created by PhpStorm.
 * User: luoxulx
 * Date: 2019/7/19
 * Time: 下午3:35
 */

namespace App\Models;


class Reply extends BaseModel
{

    protected $fillable = [
        'user_id',
        'comment_id',
        'nickname',
        'content',
        'origin',
        'user_agent'
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'id');
    }
}
