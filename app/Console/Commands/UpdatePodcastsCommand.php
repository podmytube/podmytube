<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Factories\UploadPodcastFactory;
use App\Models\Channel;
use App\Modules\ServerRole;
use Exception;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * will update podcast feeds.
 */
class UpdatePodcastsCommand extends Command
{
    public const FAILURE = 0;
    public const SUCCESS = 1;

    /** @var array */
    protected $messages = [];

    protected $progressBar;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:podcasts {batchToProcess=all : options are all/free/paying/early}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will build podcast feeds by kind.';

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

        $optionAndMethods = [
            'free' => 'freeChannels',
            'paying' => 'payingChannels',
            'early' => 'earlyBirdsChannels',
            'all' => 'allActiveChannels',
        ];
        $optionTyped = $this->argument('batchToProcess');
        if (!isset($optionAndMethods[$optionTyped])) {
            throw new RuntimeException(
                "Option {$optionTyped} is not a valid one. Options available : free/paying/early/all."
            );
        }
        $method = $optionAndMethods[$optionTyped];
        $channels = Channel::$method();

        if (!$channels->count()) {
            $this->info(
                "There is no channels to generate for option {{$optionTyped}}",
                'v'
            );

            return true;
        }

        if ($this->getOutput()->isVerbose()) {
            $this->progressBar = $this->output->createProgressBar(
                count($channels)
            );
            $this->progressBar->start();
        }

        $channels->map(function ($channel): void {
            try {
                UploadPodcastFactory::for($channel)->run();
                $this->recordSuccess($channel);
            } catch (Exception $exception) {
                $this->recordFailure($channel, $exception);
            }
            if ($this->getOutput()->isVerbose()) {
                $this->progressBar->advance();
            }
        });

        if ($this->getOutput()->isVerbose()) {
            $this->progressBar->finish();
            $this->line('');
            $this->info(implode(PHP_EOL, $this->getSuccess()));
        }

        // Used with a crontab errors will be sent by email if any.
        if ($this->getErrors()) {
            $this->error(implode(PHP_EOL, $this->getErrors()));
        }

        return 0;
    }

    protected function recordSuccess(Channel $channel): void
    {
        $this->addMessage(
            self::SUCCESS,
            "Channel {$channel->title()} {{$channel->channelId()}} has been successfully generated {" .
                $channel->podcastUrl() .
                '}'
        );
    }

    protected function recordFailure(Channel $channel, $exception): void
    {
        $this->addMessage(
            self::FAILURE,
            "Podcast generation has failed for Channel {$channel->title()} {{$channel->channelId()}} with " .
                $exception->getMessage() .
                PHP_EOL
        );
    }

    protected function addMessage($key, $value): void
    {
        $this->messages[$key][] = $value;
    }

    protected function getErrors()
    {
        return $this->messages[self::FAILURE] ?? null;
    }

    protected function getSuccess()
    {
        return $this->messages[self::SUCCESS] ?? null;
    }
}
