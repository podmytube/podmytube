<?php

use App\Channel;
use App\Subscription;
use App\Services\ChannelPremiumToSubscriptionService;
use Illuminate\Database\Seeder;

class SubscriptionTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (App::environment(['dev', 'local', 'rec', 'testing'])) {
            Subscription::truncate();
        }

        /**
         * getting channels informations
         */
        $channels = Channel::select(['channel_id', 'channel_name', 'channel_premium', 'channel_createdAt'])
            ->get();

        if ($channels->count()) {
            /**
             * for each channel add a subscription according to its channel_premium state
             */
            foreach ($channels as $channel) {
                try {
                    ChannelPremiumToSubscriptionService::transform($channel);
                } catch (\Exception $e) {
                    die("Channel subscription transformation has failed with message : {{$e->getMessage()}} ");
                }
            }
        }
    }
}
