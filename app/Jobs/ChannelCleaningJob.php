<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Interfaces\Podcastable;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Playlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ChannelCleaningJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Channel $channelToDelete;

    public function __construct(Channel $channelToDelete)
    {
        $this->channelToDelete = $channelToDelete;
    }

    public function handle(): void
    {
        $this->delete();
    }

    public function delete(): void
    {
        Storage::disk(SendFileBySFTP::REMOTE_DISK);

        // delete podcast file
        if (Storage::exists($this->channelToDelete->remoteFilePath())) {
            Storage::delete($this->channelToDelete->remoteFilePath());
        }

        // delete medias
        $this->channelToDelete->medias->each(fn (Media $media) => MediaCleaning::dispatch($media));

        // delete playlists
        $this->channelToDelete->playlists->map(fn (Playlist $playlist) => $playlist->delete());

        // delete subscription
        $this->channelToDelete->subscription->delete();

        // delete podcastable (channel/playlist)
        $this->channelToDelete->delete();
    }
}
