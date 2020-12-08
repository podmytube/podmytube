<?php

use App\Channel;
use App\Plan;
use App\Subscription;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChannelsTableSeeder extends Seeder
{
    public const PERSONAL_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('channels')->delete();

        $channelModel = factory(Channel::class)->create([
            'channel_id' => self::PERSONAL_CHANNEL_ID,
            'category_id' => 4, //education
            'user_id' => User::byEmail('frederick@podmytube.com')->user_id,
        ]);

        $subscriptionModel = factory(Subscription::class)->create(
            [
                'channel_id' => $channelModel->channel_id,
                'plan_id' => Plan::bySlug('weekly_youtuber')->id,
            ]
        );
    }
}
