<?php

use App\Subscription;
use App\Channel;

use Illuminate\Database\Seeder;

class subscriptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * getting channels informations
         */
        $channels = Channel::select(['channel_id', 'channel_name', 'channel_premium'])->get();

        /**
         * for each channel add a subscription according to its channel_premium state
         */
        foreach ($channels as $channel){

            Subscription::insert([
                'id'                => 1,
                'channel_id'        => $channel->channel_id
                'name'              => 'early_bird_2017',
                'price_per_month'   => 0,
                'created_at'        => Carbon::createFromDate(2017,1,1),
                'updated_at'        => Carbon::now(),
            ]);

        }
    }
}
