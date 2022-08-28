<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Exceptions\NotReadableFileException;
use App\Interfaces\InteractsWithMedia;
use App\Jobs\SendFileBySFTP;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UploadMediaListener implements ShouldQueue
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

    public function handle(InteractsWithMedia $event): void
    {
        $media = $event->media();

        $localPath = $media->uploadedFilePath();
        $remotePath = $media->remoteFilePath();

        if (!file_exists($localPath)) {
            $message = "File on {$localPath} does not exists.";
            Log::error($message);

            throw new InvalidArgumentException($message);
        }

        if (!is_readable($localPath)) {
            $message = "File on {$localPath} is not readable.";
            Log::error($message);

            throw new NotReadableFileException("File on {$localPath} does not exists.");
        }

        SendFileBySFTP::dispatchSync($localPath, $remotePath, true);
    }

    public function retryUntil(): Carbon
    {
        return now()->addMinutes(5);
    }
}
