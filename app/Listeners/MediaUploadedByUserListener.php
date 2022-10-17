<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Interfaces\InteractsWithMedia;
use App\Interfaces\InteractsWithPodcastable;
use App\Jobs\MediaUploadedByUserJob;
use App\Jobs\UploadPodcastJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class MediaUploadedByUserListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InteractsWithMedia|InteractsWithPodcastable $event): void
    {
        Bus::chain([
            // transfer file from www to host
            new MediaUploadedByUserJob($event->media()),
            // THEN ! rebuild podcast
            new UploadPodcastJob($event->podcastable()),
        ])->dispatch();
    }
}
