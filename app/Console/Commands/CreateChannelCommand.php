<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Factories\CreateChannelFactory;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use RuntimeException;

class CreateChannelCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:channel {channel_id} {--userId=1} {--planId=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create channel from one valid channel_id.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->prologue();

        try {
            $this->line('');
            $userId = $this->option('userId');
            $user = User::find($userId);
            if ($user === null) {
                throw new RuntimeException("This user id {$userId} is unknown in database.");
            }

            $youtubeUrl = 'https://www.youtube.com/channel/' . $this->argument('channel_id');

            $planId = $this->option('planId');
            $plan = Plan::find($planId);
            if ($plan === null) {
                throw new RuntimeException("This plan id {$planId} does not exists.");
            }

            // creating channel
            $channel = CreateChannelFactory::fromYoutubeUrl(user: $user, youtubeUrl: $youtubeUrl, active: true);

            // adding subscription
            Subscription::query()
                ->updateOrCreate(
                    ['channel_id' => $channel->channelId()],
                    [
                        'channel_id' => $channel->channelId(),
                        'plan_id' => $plan->id,
                    ]
                )
            ;

            $this->info('Channel 🎉 ' . $channel->nameWithId() . ' 🎉 has been created successfully !');
            $this->line('');

            $errCode = 0;
        } catch (Exception $exception) {
            $this->error($exception->getMessage());

            $errCode = 1;
        }

        $this->epilogue();

        return $errCode;
    }
}
