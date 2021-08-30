<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Modules\ServerRole;
use App\Youtube\YoutubePlaylists;
use Illuminate\Console\Command;

class YoutubePlaylistsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:playlists {channelId} {--raw}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the playlists for specified channel';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

        $this->info('===========================================');
        $this->info("Getting playlists for {$this->argument('channelId')}");
        $this->info('===========================================');
        $factory = YoutubePlaylists::init()->forChannel($this->argument('channelId'));

        if ($this->option('raw')) {
            print_r($factory->playlists());

            return 0;
        }

        array_map(function ($playlist): void {
            $this->line("Playlist {$playlist['title']} ({$playlist['id']}) - nb videos : {{$playlist['nbVideos']}}");
        }, $factory->playlists());

        return 0;
    }
}
