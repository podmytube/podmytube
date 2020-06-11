<?php

namespace App\Console\Commands;

use App\Channel;
use App\Mail\LastMediaNotGrabbedMail;
use App\Media;
use App\Youtube\YoutubeChannelVideos;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Mail;

class LastMediaPublishedChecker extends Command
{
    public const NB_HOURS_AGO = 6;
    /**
     * @var array App\Channels[] $channelsInTrouble
     */
    protected $channelsInTrouble = [];
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

            /**
             * if published recently, we are letting a little more time.
             */
            if ($this->hasBeenPublishedRecently($lastVideo['published_at'])) {
                return;
            }

            /**
             * media has been published some hours ago.
             * has it been grabbed
             */
            if ($this->HasMediaBeenGrabbed($lastVideo['media_id'])) {
                /**
                 * media has been grabbed everything ok.
                 */
                return;
            }

            $this->addChannelInTrouble($channelToCheck);
        });

        if (count($this->channelsInTrouble)) {
            /**
             * Send myself an email with channels in trouble
             */
            Mail::to(env('EMAIL_TO_WARN'))->queue(
                new LastMediaNotGrabbedMail($this->channelsInTrouble)
            );
        }
        $this->comment("It's all folks.", 'v');
    }

    protected function addChannelInTrouble(Channel $channel)
    {
        $this->channelsInTrouble[] = $channel;
    }

    protected function HasMediaBeenGrabbed(string $mediaId): bool
    {
        try {
            $media = Media::findOrFail($mediaId);
            return $media->hasBeenGrabbed();
        } catch (ModelNotFoundException $exception) {
            // unknown in DB => not grabbed ...
            return false;
        }
    }

    /**
     * check if media has been published recently.
     */
    protected function hasBeenPublishedRecently(Carbon $publishedAt): bool
    {
        return $publishedAt->isAfter($this->someHoursAgo);
    }
}
