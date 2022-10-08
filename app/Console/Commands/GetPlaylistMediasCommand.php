<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Models\Media;
use App\Models\Playlist;
use App\Modules\ServerRole;
use App\Youtube\YoutubePlaylistItems;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetPlaylistMediasCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:playlistitems';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will get playlists medias for all active channels';

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
        $this->prologue();

        /**
         * get active playlists.
         */
        $playlists = Playlist::active()->get();

        $nbPlaylists = count($playlists);
        if (!$nbPlaylists) {
            $message = 'There is no active playlist to refresh.';
            $this->error($message);
            Log::notice($message);

            return 1;
        }

        $playlists->each(function ($playlist): void {
            /** for each playlist, get the media to obtain */
            $factory = YoutubePlaylistItems::init()
                ->forPlaylist($playlist->youtube_playlist_id)
                ->run()
            ;
            $channelToUpdate = $playlist->channelId();

            /** keeping only channel own videos */
            $onlyThisChannelVideos = array_filter(
                $factory->items(),
                function ($video) use ($channelToUpdate) {
                    return $video['snippet']['channelId'] === $channelToUpdate;
                }
            );

            /** for each playlist item */
            $nbVideosToKeep = count($onlyThisChannelVideos);
            array_map(function ($video) use ($channelToUpdate): void {
                Media::updateOrCreate(
                    [
                        'media_id' => $video['snippet']['resourceId']['videoId'],
                    ],
                    [
                        'channel_id' => $channelToUpdate,
                        'title' => $video['snippet']['title'],
                        'description' => $video['snippet']['description'],
                        'published_at' => Carbon::parse($video['snippet']['publishedAt'])->setTimezone('UTC'),
                    ]
                );
            }, $onlyThisChannelVideos);

            Log::notice("Playlist {$playlist->youtube_playlist_id} updated with {$nbVideosToKeep} videos.");
        });

        $this->epilogue();

        return 0;
    }
}
