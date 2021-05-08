<?php

namespace App\Console\Commands;

use App\Channel;
use App\Factories\UploadPodcastFactory;
use App\Playlist;
use Illuminate\Console\Command;

class UpdatePlaylistsForChannelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:playlist {channel_id} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will update playlists podcast for specific channel';

    /** @var \App\Youtube\YoutubeCore $youtubeCore */
    protected $youtubeCore;

    /** @var array $channels list of channel models */
    protected $channels = [];

    /** @var array $errors list of errors that occured */
    protected $errors = [];

    /** @var \Symfony\Component\Console\Helper\ProgressBar $bar */
    protected $bar;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $channelToUpdate = Channel::byChannelId($this->argument('channel_id'));

        // no channel to refresh => nothing to do
        if ($channelToUpdate === null) {
            $this->error("There is no channel with this channel_id ({$this->argument('channel_id')})");
            return 1;
        }

        $this->info('channel to update ' . $channelToUpdate->channel_id, 'v');

        /**
         * getting active playlists
         */
        $playlists = $channelToUpdate->playlists()->where('active', '=', 1)->get();
        if ($playlists->count() <= 0) {
            $this->error("This channel ({$this->argument('channel_id')}) has no active playlists.");
            return 1;
        }

        $playlists->map(function (Playlist $playlist) {
            UploadPodcastFactory::init()->for($playlist);

            $this->comment("Playlist {$playlist->podcastTitle()} has been successfully updated.", 'v');
            $this->info("You can check it here : {$playlist->podcastUrl()}", 'v');
        });

        return 0;
    }
}
