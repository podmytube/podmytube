<?php

declare(strict_types=1);

namespace App\Interfaces;

use Carbon\Carbon;

interface Sitemapable
{
    public function sitemapLoc(): string;

    public function sitemapLastmod(): Carbon;

    public function sitemapChangeFreq(): string;

    public function sitemapPriority(): string;
}
