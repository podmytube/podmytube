<?php

declare(strict_types=1);

namespace App;

use App\Interfaces\Sitemapable;
use App\Traits\IsSitemapable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements Sitemapable
{
    use IsSitemapable;

    public string $sitemapRouteName = 'post.show';
    public string $sitemapPriority = '1.0';

    protected $guarded = [];
    protected $casts = [
        'sticky' => 'boolean',
    ];

    // protected $dates = ['created_at', 'updated_at', 'published_at'];

    public static function byWordpressId(int $wpId)
    {
        return self::where('wp_id', '=', $wpId)->first();
    }

    public function postCategory()
    {
        return $this->belongsTo(PostCategory::class);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function title()
    {
        return html_entity_decode($this->title);
    }

    public function lastUpdate()
    {
        return $this->updated_at->format('l jS \\of F Y');
    }
}
