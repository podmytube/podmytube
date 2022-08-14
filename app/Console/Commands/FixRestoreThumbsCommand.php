<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\Thumb;
use App\Modules\ServerRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixRestoreThumbsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:restore-thumbs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

        /** scan directory where thumbs are located */
        $folders = Storage::disk('remote')->directories('thumbs.podmytube.com/www');

        // for each folder
        array_map(function ($folderPath) {
            $this->info("working with {$folderPath}", 'v');

            /** extracting channel id */
            $channel = $this->getChannelFromPath($folderPath);
            if ($channel === null) {
                // this channel id has no channel
                return false;
            }

            $this->info("for channel {$channel->nameWithId()}, setting candidate thumbs", 'v');

            /** get all files in folder */
            $filesInChannelFolder = Storage::disk('remote')->files($folderPath);
            array_map(function ($thumbFilePath) use ($channel) {
                $filename = $this->lastPartFromPath($thumbFilePath);
                $thumb = Thumb::where('file_name', '=', $filename)->first();
                $this->info("Looking for {$filename} in thumbs.", 'v');
                if ($thumb === null) {
                    // this filename is not a thumb
                    return false;
                }

                $this->info("let's associate thumb {$filename} with {$thumb->id}", 'v');
                $thumb->update([
                    'coverable_type' => $channel->morphedName(),
                    'coverable_id' => $channel->id(),
                ]);
            }, $filesInChannelFolder);
        }, $folders);

        return 0;
    }

    public function getChannelFromPath(string $folderPath)
    {
        $channelId = $this->lastPartFromPath($folderPath);

        return Channel::byChannelId($channelId);
    }

    public function lastPartFromPath(string $path): string
    {
        $explodedPath = explode(DIRECTORY_SEPARATOR, $path);

        return $explodedPath[count($explodedPath) - 1];
    }
}
