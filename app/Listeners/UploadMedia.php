<?php

namespace App\Listeners;

use App\Events\MediaUploadedByUser;
use App\Jobs\SendFileBySFTP;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UploadMedia implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(MediaUploadedByUser $event)
    {
        Log::debug('--- ' . __CLASS__ . ' start');
        /** @var \App\Channel $channel */
        $media = $event->media;

        $localPath = $media->uploadedFilePath();
        $remotePath = $media->remoteFilePath();

        if (!file_exists($localPath)) {
            $message = "File on {$localPath} does not exists.";
            Log::error($message);
            throw new InvalidArgumentException($message);
        }

        SendFileBySFTP::dispatchNow($localPath, $remotePath, true);
        Log::debug('--- ' . __CLASS__ . ' start');
    }
}
