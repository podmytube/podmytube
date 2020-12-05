<?php

namespace App\Listeners;

use App\Events\ThumbUpdated;
use App\Jobs\SendThumbBySFTP;

class UploadThumb
{
    public function handle(ThumbUpdated $event)
    {
        SendThumbBySFTP::dispatchNow($event->channel->thumb);
    }
}
