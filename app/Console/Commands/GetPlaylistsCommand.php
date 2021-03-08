<?php

namespace App\Console\Commands;

use App\Channel;
use App\Exceptions\NoPayingChannelException;
use App\Playlist;
use App\Youtube\YoutubePlaylists;
use Illuminate\Console\Command;

class GetPlaylistsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:playlists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will obtain playlists for all paying channels';

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
         * add now tech
         */
        $nowtech = Channel::find('UCRU38zigLJNtMIh7oRm2hIg');
        if ($nowtech !== null) {
            $channels->push($nowtech);
        }

        /**
         * get playlists from youtube
         */
        $nbPlaylists = 0;
        $channels->map(function (Channel $channel) use (&$nbPlaylists) {
            $this->comment('======================================================================', 'v');
            $this->comment("Getting playlists (from youtube) for {$channel->nameWithId()}", 'v');
            $playlists = ((new YoutubePlaylists())->forChannel($channel->channelId())->playlists());
            $nbPlaylists += count($playlists);
            array_map(function ($playlistItem) use ($channel) {
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
        });

        $this->info("Nb playlists added/updated : {$nbPlaylists}", 'v');
    }
}
