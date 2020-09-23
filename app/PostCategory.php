<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    protected $guarded = [];

    public static function byWordpressId(int $wpId)
    {
        return self::where('wp_id', '=', $wpId)->first();
    }

    public static function bySlug(string $slug)
    {
        return self::where('slug', '=', strtolower($slug))->first();
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
