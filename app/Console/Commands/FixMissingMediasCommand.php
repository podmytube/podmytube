<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Factories\LineLogParserFactory;
use App\Jobs\DownloadMediaJob;
use App\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Spatie\Ssh\Ssh;

class FixMissingMediasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:missing-medias {duration=1h}';

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
        $remoteCommandToBeRun = 'docker logs --since=' . $this->argument('duration') . ' ' . config('app.audio_container_name') . ' | grep 404';
        $this->info("Remote command to be run : {$remoteCommandToBeRun}", 'v');
        $sshProcess = Ssh::create(config('app.sftp_user'), config('app.sftp_host'))
            ->usePrivateKey(config('app.sftp_key_path'))
            ->disableStrictHostKeyChecking()
            ->execute($remoteCommandToBeRun)
        ;

        if (!$sshProcess->isSuccessful()) {
            $this->error('docker logs command over ssh has failed with error ' . $sshProcess->getErrorOutput());

            return 1;
        }

        array_map(function (string $lineLog): void {
            $this->processLineLog($lineLog);
        }, explode(PHP_EOL, $sshProcess->getOutput()));

        return 0;
    }

    public function processLineLog(string $lineLog): void
    {
        if (!strlen($lineLog)) {
            // empty line => skip
            return;
        }

        $lineLogParser = LineLogParserFactory::read($lineLog)->parse();
        if ($lineLogParser->isSuccessful()) {
            // not a 404 one => skip
            return;
        }

        $mediaId = $lineLogParser->mediaId();
        if ($mediaId === null) {
            // line log without mediaId => skip
            return;
        }

        $media = Media::byMediaId($mediaId, true);
        if ($media !== null) {
            // this media is to be downloaded --force
            DownloadMediaJob::dispatch($media, true);

            return;
        }
        // strange case !!!!
        Log::debug("Media {$mediaId} is missing from a feed but it is unknown in DB too.");
    }
}
