<?php

namespace App\Console\Commands;

use App\Channel;
use App\Mail\LastMediaNotGrabbedMail;
use App\Media;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeVideo;
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
        $this->channelsToCheck = Channel::payingChannels();

        /**
         * add now tech
         */
        $this->addNowTech();

        /**
         * get last episode
         */
        $this->channelsToCheck->map(function ($channelToCheck) {
            ($videos = new YoutubeChannelVideos())
                ->forChannel($channelToCheck->channel_id, 1)
                ->videos();
            $lastVideo = $videos->videos()[0];

            $this->info(
                "Checking media {$lastVideo['media_id']} for {$channelToCheck->channel_name}",
                'v'
            );

            if ($this->hasBeenPublishedRecently($lastVideo['published_at'])) {
                /**
                 * if published recently, we are letting a little more time.
                 */
                return;
            }

            if (!(new YoutubeVideo($lastVideo['media_id']))->isAvailable()) {
                /**
                 * If video is not available (upcoming live) do not send an alert
                 */
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
            $this->error(
                "Channel {$channelToCheck->channel_name} is in trouble !",
                'v'
            );
            $this->addChannelInTrouble($channelToCheck);
        });

        $this->info(
            'Nb paying channels in trouble : ' .
                count($this->channelsInTrouble),
            'v'
        );
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

    /**
     * add now tech
     */
    protected function addNowTech()
    {
        $nowtech = Channel::find('UCRU38zigLJNtMIh7oRm2hIg');
        if ($nowtech !== null) {
            $this->channelsToCheck->push($nowtech);
        }
    }
}
