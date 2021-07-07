<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Channel;
use App\Interfaces\Podcastable;
use App\Media;
use App\Playlist;
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

    /** @var \App\Channel */
    protected $channelToDelete;

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
        $this->channelToDelete->medias->map(function (Media $media): void {
            MediaCleaning::dispatch($media);
        });

        // delete playlists
        $this->channelToDelete->playlists->map(function (Playlist $playlist): void {
            $playlist->delete();
        });

        $this->channelToDelete->subscription->delete();

        // delete podcastable (channel/playlist)
        $this->channelToDelete->delete();
    }
}
