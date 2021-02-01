<?php

namespace App\Console\Commands;

use App\Channel;
use App\Exceptions\NoPayingChannelException;
use App\Factories\UploadPodcastFactory;
use App\Playlist;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdatePlaylistsForPayingChannelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:playlists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will update playlists podcast for all paying channels';

    /** @var \App\Youtube\YoutubeCore $youtubeCore */
    protected $youtubeCore;

    /** @var \App\Channels[] $channels list of channel models */
    protected $channels = [];

    /** @var string[] $errors list of errors that occured */
    protected $errors = [];

    /** @var \Symfony\Component\Console\Helper\ProgressBar $bar */
    protected $bar;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /**
         * get paying channels
         */
        $channels = Channel::payingChannels();
        if ($channels === null) {
            throw new NoPayingChannelException('There is no paying channel to get playlist for.');
        }

        /**
         * getting active playlists
         */
        $channels->map(function ($channel) {
            $playlists = $channel->playlists()->where('active', '=', 1)->get();
            if ($playlists->count() <= 0) {
                $message = "This channel ({$channel->channelId()}) has no active playlists.";
                $this->info($message, 'v');
                Log::debug($message);
                return;
            }

            $playlists->map(function (Playlist $playlist) {
                UploadPodcastFactory::init()->for($playlist);

                $this->comment("Playlist {$playlist->podcastTitle()} has been successfully updated.", 'v');
                $this->info("You can check it here : {$playlist->podcastUrl()}", 'v');
            });
        });
    }
}
