<?php

namespace App\Console\Commands;

use App\Channel;
use App\Jobs\SendFeedBySFTP;
use App\Podcast\PodcastBuilder;
use App\Podcast\PodcastUrl;

use Illuminate\Console\Command;
use RuntimeException;

class BatchPodcasts extends Command
{
    protected const _FAILURE = 0;
    protected const _SUCCESS = 1;

    protected $messages = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcast:batch {batchToProcess=all : options are all/free/paying/early}';

    /* {--all : will generate all active podcasts } 
        {--free : will generate only the free ones } 
        {--early : will generate only the early birds } 
        {--paying : will generate only paying channels }'; */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will build podcast feeds by kind.';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $channels = null;
        switch ($optionTyped = $this->argument('batchToProcess')) {
            case 'free':
                $channels = Channel::freeChannels();
                break;
            case 'paying':
                $channels = Channel::payingChannels();
                break;
            case 'early':
                $channels = Channel::earlyBirdsChannels();
                break;
            case 'all':
                $channels = Channel::allActiveChannels();
                break;
            default:
                throw new RuntimeException("Option $optionTyped is not a valid one. Options available : free/paying/early/all.");
        }

        if (!$channels->count()) {
            if ($this->getOutput()->isVerbose()) {
                $this->info("There is no channels to generate for option {{$optionTyped}}");
            }
            return true;
        }

        if ($this->getOutput()->isVerbose()) {
            $bar = $this->output->createProgressBar(count($channels));
            $bar->start();
        }

        foreach ($channels as $channel) {
            try {
                if (PodcastBuilder::prepare($channel)->save()) {
                    // uploading feed
                    SendFeedBySFTP::dispatchNow($channel);
                    $this->recordSuccess($channel);
                }
            } catch (\Exception $exception) {
                $this->recordFailure($channel, $exception);
            }
            if ($this->getOutput()->isVerbose()) {
                $bar->advance();
            }
        }

        if ($this->getOutput()->isVerbose()) {
            $bar->finish();
        }

        if ($this->getOutput()->isVeryVerbose()) {
            $this->info(
                PHP_EOL . implode(PHP_EOL, $this->getSuccess())
            );
        }

        if ($this->getErrors()) {
            $this->error($this->getErrors());
        }
    }

    protected function recordSuccess(Channel $channel)
    {
        $this->addMessage(
            self::_SUCCESS,
            "Channel {$channel->title()} {{$channel->channelId()}} has been successfully generated {" .
                PodcastUrl::prepare($channel)->get() . "}"
        );
    }

    protected function recordFailure(Channel $channel, $exception)
    {
        $this->addMessage(
            self::_FAILURE,
            "Podcast generation has failed for Channel {$channel->title()} {{$channel->channelId()}} with "
                . $exception->getMessage() . PHP_EOL
        );
    }

    protected function addMessage($key, $value)
    {
        $this->messages[$key][] = $value;
    }

    protected function getErrors()
    {
        return $this->messages[self::_FAILURE] ?? null;
    }

    protected function getSuccess()
    {
        return $this->messages[self::_SUCCESS] ?? null;
    }
}
