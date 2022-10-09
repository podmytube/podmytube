<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Factories\UploadPodcastFactory;
use App\Interfaces\Podcastable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadPodcastJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected UploadPodcastFactory $uploadFactory;

    /**
     * Create a new job instance.
     */
    public function __construct(public Podcastable $podcastable)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->uploadFactory = UploadPodcastFactory::for($this->podcastable)->run();
    }

    public function localFilePath(): string
    {
        return $this->uploadFactory->prepareLocalPath();
    }

    public function remoteFilePath(): string
    {
        return $this->uploadFactory->remotePath();
    }
}
