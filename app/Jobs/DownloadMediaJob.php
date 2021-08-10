<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Factories\DownloadMediaFactory;
use App\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DownloadMediaJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(Media $mediaToDownload, bool $force = false)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__);
        $this->media = $mediaToDownload;
        $this->force = $force;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DownloadMediaFactory::media($this->media)->run();
    }
}
