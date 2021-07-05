<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Interfaces\Podcastable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PodcastableCleaning implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var \App\Interfaces\Podcastable */
    protected $podcastableToDelete;

    public function __construct(Podcastable $podcastableToDelete)
    {
        $this->podcastableToDelete = $podcastableToDelete;
    }

    public function handle(): void
    {
        $this->delete();
    }

    public function delete(): void
    {
        Storage::disk(SendFileBySFTP::REMOTE_DISK);

        // delete podcast file
        if (Storage::exists($this->podcastableToDelete->remoteFilePath())) {
            Storage::delete($this->podcastableToDelete->remoteFilePath());
        }

        // delete medias
        $this->podcastableToDelete->associatedMedias()->map(function ($media): void {
            dump("dispatching media cleaning for {$media->media_id}");
            MediaCleaning::dispatch($media);
        });

        // delete podcastable (channel/playlist)
        $this->podcastableToDelete->delete();
    }
}
