<?php

use App\Channel;
use App\Plan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class ChannelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment(['local']) && env('DB_CONNECTION') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Channel::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            /**
             * my own channel
             */
            $this->createChannelWithSub(
                [
                    'channel_id' => 'UCw6bU9JT_Lihb2pbtqAUGQw',
                    'user_id' => 1,
                    'channel_name' => 'Frederick Tyteca',
                ],
                Plan::find(Plan::FREE_PLAN_ID)
            );
        }
    }

    public function createChannelWithSub(array $channelInfos, Plan $plan)
    {
        $channel = factory(App\Channel::class)->create($channelInfos);
        factory(App\Subscription::class)->create([
            'channel_id' => $channel->channel_id,
            'plan_id' => $plan->id,
        ]);
    }
}
