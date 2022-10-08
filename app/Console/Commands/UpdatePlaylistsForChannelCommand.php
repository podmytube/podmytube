<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Factories\UploadPodcastFactory;
use App\Models\Channel;
use App\Models\Playlist;
use App\Modules\ServerRole;
use Illuminate\Console\Command;

class UpdatePlaylistsForChannelCommand extends Command
{
    use BaseCommand;

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
            $this->error("This channel ({$this->argument('channel_id')}) has no active playlists.", 'v');

            return 1;
        }

        $playlists->map(function (Playlist $playlist): void {
            UploadPodcastFactory::for($playlist)->run();
            $this->comment("Playlist {$playlist->podcastTitle()} has been successfully updated.", 'v');
            $this->info("You can check it here : {$playlist->podcastUrl()}", 'v');
        });
        $this->epilogue();

        return Command::SUCCESS;
    }
}
