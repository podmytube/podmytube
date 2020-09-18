<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public const STATUS_PUBLISHED = 1;

    protected $guarded = [];

    public static function byWordpressId(int $wpId)
    {
        return self::where('wp_id', '=', $wpId)->first();
    }

    public function postCategory()
    {
        return $this->belongsTo(PostCategory::class);
    }
}
