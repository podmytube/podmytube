<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\ChannelUpdated;
use App\Models\Media;
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
        Storage::disk(SendFileBySFTP::REMOTE_DISK);

        // delete file
        if (Storage::exists($this->mediaToDelete->remoteFilePath())) {
            Log::notice("This file {$this->mediaToDelete->remoteFilePath()} exists => delete.");
            Storage::delete($this->mediaToDelete->remoteFilePath());
        } else {
            Log::notice("This file {$this->mediaToDelete->remoteFilePath()} does not exist. It cannot be deleted.");
        }

        // soft deleting db entry
        $this->mediaToDelete->update(['length' => 0, 'duration' => 0, 'grabbed_at' => null]);
        $this->mediaToDelete->delete();

        // sending event to rebuild podcast
        event(new ChannelUpdated($this->mediaToDelete->channel));
    }
}
