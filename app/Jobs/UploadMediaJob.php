<?php

namespace App\Jobs;

use App\Events\ChannelUpdated;
use App\Exceptions\MediaToUploadDoesNotExistsException;
use App\Media;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        /**
         * checking file to upload is do exists
         */
        $filenameToUpload = $this->media->media_id . Media::FILE_EXTENSION;
        if (!Storage::disk('uploadedMedias')->exists($filenameToUpload)) {
            throw new MediaToUploadDoesNotExistsException('The media that should be here ' . Storage::disk('uploadedMedias')->path($filenameToUpload) . ' is not here.');
        }

        Log::notice('Uploading Media from ' . Storage::disk('uploadedMedias')->path($filenameToUpload) . '.');

        $this->media->uploadFromPath(Storage::disk('uploadedMedias')->path($filenameToUpload));
        $this->media->grabbed_at = Carbon::now();
        $this->media->save();
        Log::notice("Media should be available there {$this->media->url()}. Firing event ChannelUpdated.");

        Log::notice("channel {$this->media->channel->channel_name} ({$this->media->channel->channel_id}) should be updated");
        ChannelUpdated::dispatch($this->media->channel);
    }
}
