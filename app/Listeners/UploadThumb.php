<?php

namespace App\Listeners;

use App\Events\ThumbUpdated;
use App\Jobs\SendFileBySFTP;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UploadThumb implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ThumbUpdated $event)
    {
        Log::debug('--- ' . __CLASS__ . ' start');
        /** @var \App\Channel $channel */
        $channel = $event->channel;

        $localPath = $channel->thumb->localFilePath();
        $remotePath = $channel->thumb->remoteFilePath();

        if (!file_exists($localPath)) {
            $message = "File on {$localPath} does not exists.";
            Log::error($message);
            throw new InvalidArgumentException($message);
        }

        SendFileBySFTP::dispatchNow($localPath, $remotePath);
        Log::debug('--- ' . __CLASS__ . ' end');
    }
}
