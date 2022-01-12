<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Channel;
use App\Exceptions\NoPayingChannelException;
use App\Exceptions\YoutubeNoResultsException;
use App\Mail\ChannelIsInTroubleWarningMail;
use App\Modules\LastMediaChecker;
use App\Modules\ServerRole;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class LastMediaPublishedChecker extends Command
{
    public const NB_HOURS_AGO = 6;

    protected Collection $channelsToCheck;

    protected array $channelInTroubleMessages = [];

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

        // get paying channels
        $this->channelsToCheck = Channel::payingChannels();

        // add now tech
        $this->addNowTech();

        // remove accropolis
        $this->removeChannel('UCq80IvL314jsE7PgYsTdw7Q');

        if (!$this->channelsToCheck->count()) {
            throw new NoPayingChannelException();

            return 1;
        }

        // get last episode
        $this->channelsToCheck->map(function ($channelToCheck): void {
            $this->info("Checking channel {$channelToCheck->channel_name} ({$channelToCheck->channel_id}) .", 'v');

            try {
                $factory = LastMediaChecker::forChannel($channelToCheck)->run();
                if ($factory->shouldMediaBeingGrabbed()) {
                    $this->channelInTroubleMessages[] = "Channel {$channelToCheck->channel_name} ({$channelToCheck->channel_id}) "
                        . 'last video ' . $factory->lastMediaFromYoutube()['media_id'] . ' has not been grabbed.';
                }
            } catch (YoutubeNoResultsException $exception) {
                $this->channelInTroubleMessages[] = "Channel {$channelToCheck->channel_name} ({$channelToCheck->channel_id}) "
                    . 'has no video. It is strange.';
            }
        });

        $this->comment('Nb paying channels in trouble : ' . count($this->channelInTroubleMessages), 'v');

        if (count($this->channelInTroubleMessages)) {
            // Send myself an email with channels in trouble
            $this->comment(implode("\n", $this->channelInTroubleMessages), 'v');
            Mail::to(config('mail.email_to_warn'))
                ->queue(new ChannelIsInTroubleWarningMail($this->channelInTroubleMessages))
            ;
        }
        $this->info("It's all folks.", 'v');

        return 0;
    }

    /**
     * add now tech.
     */
    protected function addNowTech(): void
    {
        $nowtech = Channel::find('UCRU38zigLJNtMIh7oRm2hIg');
        if ($nowtech !== null) {
            $this->channelsToCheck->push($nowtech);
        }
    }

    /**
     * remove channel
     * typically, accropolis has a problem on his channel but he does not answer my emails.
     */
    protected function removeChannel(string $channelIdToRemove): void
    {
        $this->channelsToCheck->filter(function ($channel) use ($channelIdToRemove) {
            return $channel->channel_id === $channelIdToRemove;
        });
    }
}
