<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\Playlist;
use App\Modules\ServerRole;
use App\Youtube\YoutubePlaylists;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetPlaylistsForChannelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:playlistsForChannel {channel_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will obtain playlists for all paying channels';

    /** @var \App\Youtube\YoutubeCore */
    protected $youtubeCore;

    /** @var array list of channel models */
    protected $channels = [];

    /** @var array list of errors that occured */
    protected $errors = [];

    /** @var \Symfony\Component\Console\Helper\ProgressBar */
    protected $bar;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

        $channel = Channel::byChannelId($this->argument('channel_id'));
        if ($channel === null) {
            $message = "There is no channel with this channel_id ({$this->argument('channel_id')})";
            $this->error($message);
            Log::error($message);

            return 1;
        }

        /**
         * get playlists from youtube.
         */
        $nbPlaylists = 0;
        $this->comment('======================================================================', 'v');
        $this->comment("Getting playlists (from youtube) for {$channel->nameWithId()}", 'v');
        $playlists = (new YoutubePlaylists())->forChannel($channel->channelId())->playlists();
        $nbPlaylists += count($playlists);
        array_map(function ($playlistItem) use ($channel): void {
            $this->line("Getting {$playlistItem['title']}");
            Playlist::updateOrCreate(
                ['youtube_playlist_id' => $playlistItem['id']],
                [
                    'channel_id' => $channel->channelId(),
                    'youtube_playlist_id' => $playlistItem['id'],
                    'title' => $playlistItem['title'],
                    'description' => $playlistItem['description'],
                ]
            );
        }, $playlists);

        $this->info("Nb playlists added/updated : {$nbPlaylists}", 'v');

        return 0;
    }
}
