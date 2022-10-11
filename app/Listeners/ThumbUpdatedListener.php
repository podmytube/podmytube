<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Exceptions\PodcastableHasNoCoverException;
use App\Interfaces\InteractsWithMedia;
use App\Interfaces\InteractsWithPodcastable;
use App\Interfaces\Podcastable;
use App\Jobs\SendFileByRsync;
use App\Jobs\UploadPodcastJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ThumbUpdatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public Podcastable $podcastable;
    protected string $localPath;
    protected string $remotePath;

    public function handle(InteractsWithMedia|InteractsWithPodcastable $event): void
    {
        $this->podcastable = $event->podcastable();

        throw_if(
            $this->podcastable->cover === null,
            new PodcastableHasNoCoverException(get_class($this->podcastable) . " {$this->podcastable->youtube_id} has no cover yet.")
        );

        $this->localPath = $this->podcastable->cover->localFilePath();
        $this->remotePath = $this->podcastable->cover->remoteFilePath();

        // transfer file from www to host
        SendFileByRsync::dispatch($this->localPath, $this->remotePath)->onQueue('podwww');

        // should build and upload podcast
        UploadPodcastJob::dispatch($event->podcastable())->delay(now()->addSeconds(3));
    }

    public function sourceFilePath(): string
    {
        return $this->localPath;
    }

    public function destinationFilePath(): string
    {
        return $this->remotePath;
    }
}
