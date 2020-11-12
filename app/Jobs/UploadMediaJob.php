<?php

namespace App\Jobs;

use App\Exceptions\ThumbUploadHasFailedException;
use App\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** \App\Media $media */
    protected $media;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            info('Job -- ' . __CLASS__ . '::' . __FUNCTION__);
            //$this->media->uploadFromFile($foo);
        } catch (\Exception $exception) {
            throw new ThumbUploadHasFailedException(
                "Uploading media {$this->media->title} for channel {$this->media->channel_name} ({$this->media->channel_id}) has failed with message :" .
                    $exception->getMessage()
            );
        }
    }
}
