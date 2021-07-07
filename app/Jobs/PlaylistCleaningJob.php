<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Interfaces\Podcastable;
use App\Playlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PlaylistCleaningJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var \App\Playlist */
    protected $playlistToDelete;

    public function __construct(Playlist $playlistToDelete)
    {
        $this->playlistToDelete = $playlistToDelete;
    }

    public function handle(): void
    {
        $this->delete();
    }

    /**
     * Delete the playlist.
     * Will delete the playlist file, and the row in DB.
     * This will NOT remove downloaded medias because these are associated to channel.
     */
    public function delete(): void
    {
        Storage::disk(SendFileBySFTP::REMOTE_DISK);

        // delete podcast file
        if (Storage::exists($this->playlistToDelete->remoteFilePath())) {
            Storage::delete($this->playlistToDelete->remoteFilePath());
        }

        // delete podcastable (channel/playlist)
        $this->playlistToDelete->delete();
    }
}
