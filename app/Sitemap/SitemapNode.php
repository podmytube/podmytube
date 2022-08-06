<?php

declare(strict_types=1);

namespace App\Sitemap;

use App\Interfaces\Sitemapable;
use Carbon\Carbon;

class SitemapNode
{
    private function __construct(
        public string $loc,
        public ?string $changefreq = null,
        public ?Carbon $lastmod = null,
        public ?string $priority = null,
    ) {
    }

    public static function withRoute(...$params)
    {
        return new static(...$params);
    }

    public static function withSitemapable(Sitemapable $sitemapable)
    {
        return new static(
            loc: $sitemapable->sitemapLoc(),
            lastmod: $sitemapable->sitemapLastmod(),
            changefreq: $sitemapable->sitemapChangeFreq(),
            priority: $sitemapable->sitemapPriority()
        );
    }

    public function render(): string
    {
        return view('sitemap.node')
            ->with([
                'loc' => $this->loc,
                'lastmod' => $this->lastmod ? $this->lastmod->toW3cString() : Carbon::yesterday()->toW3cString(),
                'changefreq' => $this->changefreq ?? 'daily',
                'priority' => $this->priority ?? '0.8',
            ])
            ->render()
        ;
    }
}
