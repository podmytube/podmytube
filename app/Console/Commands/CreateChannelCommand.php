<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Factories\ChannelCreationFactory;
use App\Plan;
use App\User;
use Illuminate\Console\Command;

class CreateChannelCommand extends Command
{
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
        $this->line('');
        $userId = $this->option('userId');
        $user = User::find($userId);
        if ($user === null) {
            $this->error("This user id {$userId} is unknown in database.");

            return 1;
        }
        $youtubeUrl = 'https://www.youtube.com/channel/' . $this->argument('channel_id');

        $planId = $this->option('planId');
        $plan = Plan::find($planId);
        if ($plan === null) {
            $this->error("This plan id {$planId} does not exists.");

            return 1;
        }

        $factory = ChannelCreationFactory::create($user, $youtubeUrl, $plan);

        $this->info('Channel 🎉 ' . $factory->channel()->nameWithId() . ' 🎉 has been created successfully !');
        $this->line('');

        return 0;
    }
}
