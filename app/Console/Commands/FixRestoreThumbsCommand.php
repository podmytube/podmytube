<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Exceptions\SshConnectionFailedException;
use App\Models\Channel;
use App\Models\Thumb;
use App\Modules\ServerRole;
use Illuminate\Console\Command;

class FixRestoreThumbsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:restore-thumbs {channel_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore thumb if any';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->error('you should filter vignettes before using it.');

        return 1;
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

        if ($this->argument('channel_id')) {
            $channels = Channel::with('cover')->where('channel_id', '=', $this->argument('channel_id'))->get();
        } else {
            $channels = Channel::allActiveChannels();
        }

        $channels->each(function (Channel $channel): void {
            $this->info('Restoring thumb for channel ' . $channel->channel_id, 'v');

            $folderPath = 'thumbs.podmytube.com/www/' . $channel->channel_id;

            $filesWithSize = $this->getFilesFromFolder($folderPath);

            // channel has cover
            if ($channel->cover !== null) {
                if (!count($filesWithSize)) {
                    // and no thumbs in folder => remove existing cover
                    $channel->cover->delete();
                    $this->info("Channel {$channel->channel_id} has no files, invalid cover removed.", 'v');

                    return;
                }

                if (array_key_exists($channel->cover->file_name, $filesWithSize)) {
                    // ...and remote file exists => OK no need to change
                    $this->info("Channel {$channel->channel_id} cover is fine.", 'v');

                    return;
                }

                // existing cover file is missing
                // ...and there are covers in folder => assign the most recent one
                $mostRecentFileName = array_key_first($filesWithSize);
                $mostRecentFileSize = $filesWithSize[$mostRecentFileName];

                $result = $channel->cover->update(
                    [
                        'file_size' => $mostRecentFileSize,
                        'file_name' => $mostRecentFileName,
                    ]
                );
                $this->info("Channel {$channel->channel_id} cover been updated with most recent file", 'v');

                return;
            }

            // channel has no cover
            if (!count($filesWithSize)) {
                // and no thumbs in folder => no change
                $this->info("Channel {$channel->channel_id} has no cover, and there is no files.", 'v');

                return;
            }

            // ...and there are covers in folder => assign the most recent one
            $mostRecentFileName = array_key_first($filesWithSize);
            $mostRecentFileSize = $filesWithSize[$mostRecentFileName];

            $channel->cover()->create([
                'coverable_type' => $channel->morphedName(),
                'coverable_id' => $channel->id(),
                'file_size' => $mostRecentFileSize,
                'file_name' => $mostRecentFileName,
                'file_disk' => Thumb::LOCAL_STORAGE_DISK,
            ]);
            $this->info("Channel {$channel->channel_id} cover been updated with most recent file", 'v');
        });

        return 0;
    }

    public function getChannelFromPath(string $folderPath): ?Channel
    {
        $channelId = $this->lastPartFromPath($folderPath);

        return Channel::byChannelId($channelId);
    }

    public function lastPartFromPath(string $path): string
    {
        $explodedPath = explode(DIRECTORY_SEPARATOR, $path);

        return $explodedPath[count($explodedPath) - 1];
    }

    protected function getFilesFromFolder(string $folderPath): array
    {
        $sshProcess = sshPod()->execute('ls -Ati ' . config('app.podhost_ssh_root') . '/' . $folderPath);

        throw_unless($sshProcess->isSuccessful(), new SshConnectionFailedException());

        $output = trim($sshProcess->getOutput());

        $filesWithSize = [];
        if (!strlen($output)) {
            return $filesWithSize;
        }

        $lines = explode(PHP_EOL, trim($output));

        array_map(function (string $line) use (&$filesWithSize): void {
            list($filesize, $filename) = explode(' ', $line);
            $filesWithSize[$filename] = $filesize;
        }, $lines);

        return $filesWithSize;
    }
}
