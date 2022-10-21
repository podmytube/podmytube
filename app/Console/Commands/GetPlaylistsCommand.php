<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Models\Channel;
use App\Models\Playlist;
use App\Modules\ServerRole;
use App\Youtube\YoutubeCore;
use App\Youtube\YoutubePlaylists;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetPlaylistsCommand extends Command
{
    use BaseCommand;

    protected $signature = 'get:playlists {channel_id?}';
    protected $description = 'This will obtain playlists for all active/specific channel(s)';

    protected YoutubeCore $youtubeCore;
    protected array $channels = [];
    protected array $errors = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->warn('This server is not a worker.');

            return 0;
        }
        $this->prologue();
        if ($this->argument('channel_id')) {
            $channels = Channel::query()
                ->where('channel_id', '=', $this->argument('channel_id'))
                ->get()
            ;
        } else {
            $channels = Channel::active()->get();
        }

        if (!$channels->count()) {
            $message = 'There is no active channel to get playlist for or specified channel_id is invalid.';
            $this->error($message);
            Log::notice($message);

            return 1;
        }

        // get playlists from youtube.
        $channels->each(function (Channel $channel): void {
            $this->comment('======================================================================', 'v');
            $this->comment("Getting playlists (from youtube) for {$channel->nameWithId()}", 'v');

            $playlists = (new YoutubePlaylists())->forChannel($channel->youtube_id)->playlists();

            array_map(function ($playlistItem) use ($channel): void {
                $this->line("Getting {$playlistItem['title']}");
                Playlist::updateOrCreate(
                    ['youtube_playlist_id' => $playlistItem['id']],
                    [
                        'channel_id' => $channel->youtube_id,
                        'youtube_playlist_id' => $playlistItem['id'],
                        'title' => $playlistItem['title'],
                        'description' => $playlistItem['description'],
                    ]
                );
            }, $playlists);
        });

        $this->epilogue();

        return 0;
    }
}
