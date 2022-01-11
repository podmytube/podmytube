<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Channel;
use App\Factories\UploadPodcastFactory;
use App\Modules\ServerRole;
use App\Playlist;
use Illuminate\Console\Command;

class UpdatePlaylistsForChannelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:playlist {channel_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will update playlists podcast for specific channel';

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

        $channelToUpdate = Channel::byChannelId($this->argument('channel_id'));

        // no channel to refresh => nothing to do
        if ($channelToUpdate === null) {
            $this->error("There is no channel with this channel_id ({$this->argument('channel_id')})");

            return 1;
        }

        $this->info('channel to update ' . $channelToUpdate->channel_id, 'v');

        /**
         * getting active playlists.
         */
        $playlists = $channelToUpdate->playlists()->where('active', '=', 1)->get();
        if ($playlists->count() <= 0) {
            $this->error("This channel ({$this->argument('channel_id')}) has no active playlists.");

            return 1;
        }

        $playlists->map(function (Playlist $playlist): void {
            UploadPodcastFactory::for($playlist)->run();
            $this->comment("Playlist {$playlist->podcastTitle()} has been successfully updated.", 'v');
            $this->info("You can check it here : {$playlist->podcastUrl()}", 'v');
        });

        return 0;
    }
}
