<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Interfaces\InteractsWithMedia;
use App\Interfaces\InteractsWithPodcastable;
use App\Jobs\MediaUploadedByUserJob;
use App\Jobs\UploadPodcastJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MediaUploadedByUserListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InteractsWithMedia|InteractsWithPodcastable $event): void
    {
        // transfer file from www to host
        MediaUploadedByUserJob::dispatch($event->media())->onQueue('podwww');

        // rebuild podcast
        UploadPodcastJob::dispatch($event->podcastable())->delay(now()->addMinutes(10));
    }
}
