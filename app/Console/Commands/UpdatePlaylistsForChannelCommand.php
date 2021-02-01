<?php

namespace App\Console\Commands;

use App\Channel;
use App\Media;
use App\Playlist;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class UpdatePlaylistsForChannelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:playlists {channel_id} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will update playlists for specific channel';

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

        $playlists->map(function (Playlist $playlist) {
            dump($playlist->toPodcast());
        });
    }

    protected function keepingOnlyMediaIds(array $videosItems):array
    {
        /** keeping only ids */
        return array_map(function ($video) {
            return $video['media_id'];
        }, $videosItems);
    }

    protected function getGrabbedMedias(array $mediaIds):?Collection
    {
        return Media::grabbedAt()->whereIn('media_id', $mediaIds)->get();
    }
}
