<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Channel;
use Illuminate\Console\Command;

class RemoveDuplicateMediasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:duplicates {doIt=0}';

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
        $deleted = 0;
        $doIt = (bool) $this->argument('doIt');

        /** get all channels */
        $channels = Channel::all();
        $channels->map(function (Channel $channel) use ($deleted, $doIt): void {
            $mediaIdsStorage = [];
            /** get all medias */
            $medias = $channel->medias()->orderBy('created_at', 'desc')->get();
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
                ++$deleted;
            }
        });

        return 0;
    }
}
