<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\NotReadableFileException;
use App\Exceptions\UploadedMediaByUserIsMissingException;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MediaUploadedByUserJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 10;

    /**
     * Max number of fails.
     */
    public $maxExceptions = 3;

    public function __construct(protected Media $media)
    {
    }

    public function handle(): void
    {
        $localPath = $this->media->uploadedFilePath();
        $remotePath = $this->media->remoteFilePath();

        if (!file_exists($localPath)) {
            $message = "File on {$localPath} does not exists.";
            Log::error($message);

            throw new UploadedMediaByUserIsMissingException($message);
        }

        if (!is_readable($localPath)) {
            $message = "File on {$localPath} is not readable.";
            Log::error($message);

            throw new NotReadableFileException($message);
        }

        SendFileByRsync::dispatch($localPath, $remotePath, true)->onQueue('podwww');
    }

    public function retryUntil(): Carbon
    {
        return now()->addMinutes(5);
    }
}
