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
    public const JEANVIET_CHANNEL_ID = 'UCu0tUATmSnMMCbCRRYXmVlQ';

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
            'channel_id' => self::JEANVIET_CHANNEL_ID,
            'category_id' => 4, //education
            'user_id' => User::byEmail('frederick@podmytube.com')->user_id,
        ]);

        factory(Subscription::class)->create(
            [
                'channel_id' => $channel->channel_id,
                'plan_id' => Plan::bySlug('forever_free')->id,
            ]
        );
    }
}
