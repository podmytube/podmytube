<?php

use App\Channel;
use App\Plan;
use App\Subscription;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ChannelsTableSeeder extends Seeder
{
    public const PERSONAL_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';
    public const NOWTECH_LIVE_CHANNEL_ID = 'UCRU38zigLJNtMIh7oRm2hIg';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!App::environment('local')) {
            return true;
        }

        DB::table('channels')->delete();

        /** create channel */
        $channel = factory(Channel::class)->create([
            'channel_id' => self::NOWTECH_LIVE_CHANNEL_ID,
            'category_id' => 4, //education
            'user_id' => User::byEmail('frederick@podmytube.com')->user_id,
        ]);

        factory(Subscription::class)->create(
            [
                'channel_id' => $channel->channel_id,
                'plan_id' => Plan::bySlug('weekly_youtuber')->id,
            ]
        );
    }
}
