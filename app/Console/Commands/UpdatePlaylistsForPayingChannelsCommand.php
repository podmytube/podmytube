<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Exceptions\NoActiveChannelException;
use App\Factories\UploadPodcastFactory;
use App\Models\Channel;
use App\Models\Playlist;
use App\Modules\ServerRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * will update playlists feeds for every active playlist.
 */
class UpdatePlaylistsForPayingChannelsCommand extends Command
{
    use BaseCommand;

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
        $this->prologue();

        /**
         * get active channels.
         */
        $channels = Channel::active()->get();
        if ($channels === null) {
            throw new NoActiveChannelException('There is no active channel to get playlist for.');
        }

        /**
         * add now tech.
         */
        $nowtech = Channel::find('UCRU38zigLJNtMIh7oRm2hIg');
        if ($nowtech !== null) {
            $channels->push($nowtech);
        }

        // getting active playlists
        $channels->map(function ($channel): void {
            $this->comment('======================================================================', 'v');
            $this->comment("Updating playlists podcast for {$channel->nameWithId()}", 'v');
            $playlists = $channel->playlists()->where('active', '=', 1)->get();
            if ($playlists->count() <= 0) {
                $message = "This channel ({$channel->channelId()}) has no active playlists.";
                $this->info($message, 'v');
                Log::error($message);

                return;
            }

            $playlists->map(function (Playlist $playlist): void {
                UploadPodcastFactory::for($playlist)->run();
                $this->line("Playlist {$playlist->podcastTitle()} has been successfully updated.", null, 'v');
                $this->line("You can check it here : {$playlist->podcastUrl()}", null, 'v');
            });
        });

        $this->epilogue();

        return Command::SUCCESS;
    }
}
