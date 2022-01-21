<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Exceptions\FileUploadUnreadableFileException;
use App\Interfaces\InteractsWithPodcastable;
use App\Interfaces\Podcastable;
use App\Jobs\SendFileBySFTP;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UploadThumbListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 10;

    /**
     * Max number of fails.
     */
    public $maxExceptions = 3;

    protected Podcastable $podcastable;

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

        if (!is_readable($localPath)) {
            $message = "File on {$localPath} does not exists.";
            Log::error($message);

            throw new FileUploadUnreadableFileException("File on {$localPath} does not exists.");
        }

        SendFileBySFTP::dispatchSync($localPath, $remotePath);
    }

    public function retryUntil(): Carbon
    {
        return now()->addMinutes(5);
    }
}
