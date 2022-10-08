<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Channel;
use App\Models\Download;
use App\Models\Media;
use App\Models\Playlist;
use App\Models\Subscription;
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

    public function __construct(protected Channel $channelToDelete)
    {
    }

    public function handle(): void
    {
        Storage::disk(Channel::REMOTE_DISK);

        // delete podcast folder
        Storage::deleteDirectory($this->channelToDelete->feedFolderPath());

        // delete mp3 folder
        Storage::deleteDirectory($this->channelToDelete->mp3FolderPath());

        // delete playlists folder
        Storage::deleteDirectory($this->channelToDelete->playlistFolderPath());

        // delete cover folder
        Storage::deleteDirectory($this->channelToDelete->coverFolderPath());

        /*
        |--------------------------------------------------------------------------
        | cleaning db
        |--------------------------------------------------------------------------
        */
        // delete medias
        Media::query()->where('channel_id', '=', $this->channelToDelete->channel_id)->delete();

        // delete playlists db entries
        Playlist::query()->where('channel_id', '=', $this->channelToDelete->channel_id)->delete();

        // delete subscription
        Subscription::query()->where('channel_id', '=', $this->channelToDelete->channel_id)->delete();

        // delete downloads
        Download::query()->where('channel_id', '=', $this->channelToDelete->channel_id)->delete();

        // soft delete podcastable (channel/playlist)
        $this->channelToDelete->delete();
    }
}
