<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\Thumb;
use App\Modules\ServerRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\Ssh\Ssh;

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
                $this->info("this channel is unknown {$folderPath}", 'v');

                return false;
            }

            // if channels has thumb check if file exists

            $this->info("for channel {$channel->nameWithId()}, setting candidate thumbs", 'v');

            /** get all files in folder */
            // $filesInChannelFolder = Storage::disk('remote')->files($folderPath);

            $filesInChannelFolder = $this->getFilesFromFolder($folderPath);
            array_map(function ($thumbFilePath) use ($channel) {
                $filename = $this->lastPartFromPath($thumbFilePath);
                $this->info("Looking for {$filename} in thumbs.", 'v');
                $thumb = Thumb::where('file_name', '=', $filename)->first();
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

    protected function getFilesFromFolder(string $folderPath): string
    {
        $sshProcess = Ssh::create(config('app.podhost_ssh_user'), config('app.podhost_ssh_host'))
            ->disableStrictHostKeyChecking()
            ->usePrivateKey(config('app.sftp_key_path'))
            ->execute('ls -lsa /home/www/' . $folderPath)
        ;
        dd($sshProcess->getErrorOutput());
        /*
        if (!$sshProcess->isSuccessful()) {
            $message = 'docker logs command over ssh has failed with error ' . $sshProcess->getErrorOutput();
            Log::error($message);

            throw new ProcessLogsCommandHasFailedException($message);
        } */

        return '';
    }
}
