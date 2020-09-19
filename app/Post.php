<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sticky' => 'boolean',
    ];

    //protected $dates = ['created_at', 'updated_at', 'published_at'];

    public static function byWordpressId(int $wpId)
    {
        return self::where('wp_id', '=', $wpId)->first();
    }

    public function postCategory()
    {
        return $this->belongsTo(PostCategory::class);
    }
}
