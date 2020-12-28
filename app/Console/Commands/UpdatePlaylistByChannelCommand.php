<?php

namespace App\Console\Commands;

use App\Channel;
use App\Quota;
use App\Youtube\YoutubePlaylistItems;
use App\Youtube\YoutubeQuotas;
use Illuminate\Console\Command;

class UpdatePlaylistByChannelCommand extends Command
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
    protected $description = 'This will update one playlist for specific channel';

    /** @var \App\Youtube\YoutubeCore $youtubeCore */
    protected $youtubeCore;

    /** @var App\Channel[] $channels list of channel models */
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
        $channelToUpdate = Channel::byChannelId($this->argument('channel_id'));

        // no channel to refresh => nothing to do
        if ($channelToUpdate === null) {
            $this->error("There is no channel with this channel_id ({$this->argument('channel_id')})");
            return;
        }

        $this->info('channel to update ' . $channelToUpdate->channel_id, 'v');

        /**
         * getting active playlists
         */
        $playlists = $channelToUpdate->playlists()->where('playlist_active', '=', 1)->get();
        if ($playlists->count() <= 0) {
            $this->error("This channel ({$this->argument('channel_id')}) has no active playlists.");
            return;
        }

        $playlists->map(function ($playlist) {
            $videos = (new YoutubePlaylistItems)->forPlaylist($playlist->playlist_id)->videos();
            
        });

        //$apikeysAndQuotas = YoutubeQuotas::forUrls($factory->queriesUsed())->quotaConsumed();
        Quota::saveScriptConsumption(pathinfo(__FILE__, PATHINFO_BASENAME), $apikeysAndQuotas);
    }

    protected function prologue(int $nbItems)
    {
        if ($this->getOutput()->isVerbose()) {
            $this->bar = $this->output->createProgressBar($nbItems);
            $this->bar->start();
        }
    }

    protected function epilogue()
    {
        if ($this->getOutput()->isVerbose()) {
            $this->bar->finish();
        }
    }

    protected function makeProgressBarProgress()
    {
        if ($this->getOutput()->isVerbose()) {
            $this->bar->advance();
        }
    }
}
