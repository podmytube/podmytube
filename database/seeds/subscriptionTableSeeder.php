<?php

use App\Channel;
use App\Services\ChannelPremiumToSubscriptionService;
use App\Exceptions\FreePlanDoNotNeedSubscriptionException;

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
        $channels = Channel::select(['channel_id', 'channel_name', 'channel_premium', 'channel_createdAt'])
            ->where('active', 1)
            ->get();

        /**
         * for each channel add a subscription according to its channel_premium state
         */
        foreach ($channels as $channel) {

            try {
                ChannelPremiumToSubscriptionService::transform($channel);
            } catch (FreePlanDoNotNeedSubscriptionException $e) {
                // message is logged
                //echo $e->getMessage();
            } catch (\Exception $e) {
                die("Channel subscription transformation has failed with message : {{$e->getMessage()}} ");
            }

        }
    }
}
