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

    /** @var \App\Interfaces\Podcastable $podcastable */
    protected $podcastable;

    public function handle(ThumbUpdated $event)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' - start');
        $this->podcastable = $event->podcastable;

        $localPath = $this->podcastable->thumb->localFilePath();
        $remotePath = $this->podcastable->thumb->remoteFilePath();

        Log::debug("$localPath - $remotePath");

        if (!file_exists($localPath)) {
            $message = "File on {$localPath} does not exists.";
            Log::error($message);
            throw new InvalidArgumentException($message);
        }

        SendFileBySFTP::dispatchNow($localPath, $remotePath);
    }
}
