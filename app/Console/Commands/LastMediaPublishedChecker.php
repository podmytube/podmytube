<?php

namespace App\Console\Commands;

use App\Channel;
use App\Mail\LastMediaNotGrabbedMail;
use App\Modules\LastMediaChecker;
use Carbon\Carbon;
use Illuminate\Console\Command;
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
            $this->info(
                "Checking channel {$channelToCheck->channel_name} ({$channelToCheck->channel_id}) .",
                'v'
            );

            if (LastMediaChecker::forChannel($channelToCheck)->shouldMediaBeingGrabbed()) {
                $this->addChannelInTrouble($channelToCheck);
            }
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
            Mail::to(config('mail.warningRecipient'))->queue(
                new LastMediaNotGrabbedMail($this->channelsInTrouble)
            );
        }
        $this->comment("It's all folks.", 'v');
    }

    protected function addChannelInTrouble(Channel $channel)
    {
        $this->channelsInTrouble[] = $channel;
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
