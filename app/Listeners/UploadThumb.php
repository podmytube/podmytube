<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Interfaces\InteractsWithPodcastable;
use App\Jobs\SendFileBySFTP;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UploadThumb implements ShouldQueue
{
    use InteractsWithQueue;

    /** @var \App\Interfaces\Podcastable */
    protected $podcastable;

    public function handle(InteractsWithPodcastable $event): void
    {
        $this->podcastable = $event->podcastable();

        $localPath = $this->podcastable->cover->localFilePath();
        $remotePath = $this->podcastable->cover->remoteFilePath();

        if (!file_exists($localPath)) {
            $message = "File on {$localPath} does not exists.";
            Log::error($message);

            throw new InvalidArgumentException($message);
        }

        SendFileBySFTP::dispatch($localPath, $remotePath);
    }
}
