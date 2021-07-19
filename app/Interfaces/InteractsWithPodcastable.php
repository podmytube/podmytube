<?php

declare(strict_types=1);

namespace App\Interfaces;

interface InteractsWithPodcastable
{
    public function podcastable(): Podcastable;
}
