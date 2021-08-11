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

class DownloadMediaJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var \App\Media */
    protected $media;

    /** @var bool */
    protected $force;

    /**
     * Create a new job instance.
     */
    public function __construct(Media $mediaToDownload, bool $force = false)
    {
        $this->media = $mediaToDownload;
        $this->force = $force;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DownloadMediaFactory::media($this->media, $this->force)->run();
    }
}
