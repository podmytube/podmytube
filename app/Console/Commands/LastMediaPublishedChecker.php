<?php

namespace App\Console\Commands;

use App\Channel;
use App\Media;
use App\Youtube\YoutubeChannelVideos;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LastMediaPublishedChecker extends Command
{
    public const NB_HOURS_AGO = 600000000000000000000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:lastmedia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if last media on Youtube has been grabbed.';

    /** @var Carbon\Carbon $someHoursAgo some hours ago */
    protected $someHoursAgo;

    public function __construct()
    {
        parent::__construct();
        $this->someHoursAgo = Carbon::now()->subHours(self::NB_HOURS_AGO);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /**
         * get paying channels
         */
        $channelsToCheck = Channel::payingChannels();

        /**
         * get last episode
         */
        $channelsToCheck->map(function ($channelToCheck) {
            ($videos = new YoutubeChannelVideos())
                ->forChannel($channelToCheck->channel_id, 1)
                ->videos();
            $lastVideo = $videos->videos()[0];

            if ($this->hasBeenPublishedRecently($lastVideo['published_at'])) {
                return;
            }

            if ($this->HasMediaBeenGrabbed($lastVideo['media_id'])) {
                return;
            }

            $this->error(
                "{$lastVideo['title']} ({$lastVideo['media_id']}) has not been grabbed !"
            );
        });
    }

    protected function HasMediaBeenGrabbed($mediaId)
    {
        try {
            $media = Media::findOrFail($mediaId);
            return $media->hasBeenGrabbed();
        } catch (ModelNotFoundException $exception) {
            // unknown in DB => not grabbed ...
            return false;
        }
    }

    protected function hasBeenPublishedRecently(Carbon $publishedAt)
    {
        return $publishedAt->isAfter($this->someHoursAgo);
    }
}
