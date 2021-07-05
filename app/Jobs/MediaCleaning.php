<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\ChannelUpdated;
use App\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaCleaning implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $mediaToDelete;

    public function __construct(Media $mediaToDelete)
    {
        $this->mediaToDelete = $mediaToDelete;
    }

    public function handle(): void
    {
        $this->delete();
    }

    public function delete(): void
    {
        Log::debug(__CLASS__.'::'.__FUNCTION__.' deleting media '.$this->mediaToDelete->media_id);
        Storage::disk(SendFileBySFTP::REMOTE_DISK);

        // delete file
        if (Storage::exists($this->mediaToDelete->remoteFilePath())) {
            Log::debug(__CLASS__.'::'.__FUNCTION__.' deleting file '.$this->mediaToDelete->remoteFilePath());
            Storage::delete($this->mediaToDelete->remoteFilePath());
        }

        // soft deleting db entry
        $this->mediaToDelete->update(['length' => 0, 'duration' => 0, 'grabbed_at' => null]);
        $this->mediaToDelete->delete();
        Log::debug(__CLASS__.'::'.__FUNCTION__.' soft deleted '.$this->mediaToDelete->media_id.' and dispatching ChannelUpdated');

        // sending event to rebuild podcast
        event(new ChannelUpdated($this->mediaToDelete->channel));
    }
}
