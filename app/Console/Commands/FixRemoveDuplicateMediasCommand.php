<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\Media;
use App\Modules\ServerRole;
use Illuminate\Console\Command;

class FixRemoveDuplicateMediasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:remove-duplicates {channel_id?} {--doIt=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!ServerRole::isWorker()) {
            $this->warn('This server is not a worker.');

            return 0;
        }

        $channelId = $this->argument('channel_id');
        $doIt = (bool) $this->option('doIt');

        // get all channels
        if ($channelId !== null) {
            $channels = Channel::where('channel_id', '=', $channelId)->get();
        } else {
            $channels = Channel::all();
        }

        if (!$channels->count()) {
            $this->error('Either the channel you specified is unknown or there are no active channels in DB.');

            return 1;
        }

        $channels->map(function (Channel $channel) use ($doIt): void {
            $mediaIdsStorage = [];

            /** get all medias */
            $medias = Media::withTrashed()
                ->where('channel_id', '=', $channel->channel_id)
                ->orderBy('id', 'asc')
                ->get()
            ;
            if (!$medias->count()) {
                return;
            }

            foreach ($medias as $media) {
                // if not known => keep
                if (!in_array($media->media_id, $mediaIdsStorage)) {
                    $mediaIdsStorage[] = $media->media_id;
                    $this->info("keeping media {$media->id} - {$media->media_id}", 'v');

                    continue;
                }
                // if already known => delete
                $this->info("deleting duplicated media {$media->id} - {$media->media_id}", 'v');
                if ($doIt) {
                    $media->forceDelete();
                }
            }
        });

        return 0;
    }
}
