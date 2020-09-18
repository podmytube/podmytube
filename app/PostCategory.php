<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    public const NEWS = 0;

    public static function byWordpressId(int $wpId)
    {
        return self::where('wp_id', '=', $wpId)->first();
    }
}
