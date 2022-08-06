<?php

declare(strict_types=1);

namespace App\Traits;

use Carbon\Carbon;

trait IsSitemapable
{
    public function sitemapLoc(): string
    {
        return route($this->sitemapRouteName, $this);
    }

    public function sitemapLastmod(): Carbon
    {
        return $this->updated_at ?? $this->created_at;
    }

    public function sitemapChangeFreq(): string
    {
        return 'daily';
    }

    public function sitemapPriority(): string
    {
        return $this->sitemapPriority;
    }
}
