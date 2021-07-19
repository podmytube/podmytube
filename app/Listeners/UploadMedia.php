<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Interfaces\InteractsWithMedia;
use App\Jobs\SendFileBySFTP;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UploadMedia implements ShouldQueue
{
    use InteractsWithQueue;

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

        SendFileBySFTP::dispatchSync($localPath, $remotePath, true);
    }
}
