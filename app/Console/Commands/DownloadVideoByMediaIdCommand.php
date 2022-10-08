<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Subscription;
use App\Modules\CheckingGrabbedFile;
use App\Modules\DownloadYTMedia;
use App\Modules\MediaProperties;
use App\Modules\ServerRole;
use App\Youtube\YoutubeVideo;
use Exception;
use Illuminate\Console\Command;
use Tests\TestCase;

class DownloadVideoByMediaIdCommand extends Command
{
    use BaseCommand;

    /** @var string */
    protected $signature = 'download:media {media_id}';

    /** @var string */
    protected $description = 'This command will get specific video from youtube';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }
        $this->prologue();

        $media = Media::byMediaId($this->argument('media_id'));
        if ($media == null) {
            $channel = Channel::byChannelId(TestCase::PERSONAL_CHANNEL_ID);
            if ($channel == null) {
                $channel = Channel::create([
                    'channel_id' => TestCase::PERSONAL_CHANNEL_ID,
                    'user_id' => 1,
                    'channel_name' => 'Frederick Tyteca',
                    'email' => 'frederick@tyteca.net',
                ]);
                Subscription::create([
                    'channel_id' => $channel->channel_id,
                    'plan_id' => Plan::bySlug('forever_free')->id,
                ]);
            }

            $media = Media::create([
                'channel_id' => $channel->channel_id,
                'media_id' => $this->argument('media_id'),
            ]);
        }

        try {
            // getting media infos
            $this->info("Getting informations for media {$media->media_id}", 'v');
            $youtubeVideo = YoutubeVideo::forMedia($media->media_id);

            $this->info('Youtube duration for file : ' . $youtubeVideo->duration());

            // download, convert and get its path
            $this->info("About to download media {$media->media_id}.", 'v');
            $downloadedFilePath = DownloadYTMedia::init($media, '/tmp/', false)
                ->download()
                ->downloadedFilePath()
            ;

            // if empty will throw exception
            $this->info("Media {$media->media_id} has been download successfully from youtube. Analyzing.", 'v');
            $mediaProperties = MediaProperties::analyzeFile($downloadedFilePath);

            $this->info('Media properties duration : ' . $mediaProperties->duration());

            // checking obtained file duration of result
            $this->info("Checking media {$media->media_id} duration.", 'v');
            CheckingGrabbedFile::init($mediaProperties, $youtubeVideo->duration())->check();

            $media->forceDelete();
        } catch (Exception $exception) {
            $this->error($exception->getMessage(), 'v');
        }

        $this->epilogue();

        return 0;
    }
}
