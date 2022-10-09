<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Interfaces\InteractsWithMedia;
use App\Jobs\TransferMediaUploadedByUserJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MediaUploadedByUserListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InteractsWithMedia $event): void
    {
        TransferMediaUploadedByUserJob::dispatch($event->media())->onQueue('podwww');
    }
}
