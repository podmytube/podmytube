<?php

use Carbon\Carbon;
use App\Subscription;
use App\Channel;

use Illuminate\Database\Seeder;

class subscriptionTableSeeder extends Seeder
{
    /**
     * relations between old plans and newest ones
     */
    const _OLD_FREE_PLAN_ID = 0;
    const _NEW_FREE_PLAN_ID = 1;
    
    const _OLD_EARLY_PLAN_ID = 1;
    const _NEW_EARLY_PLAN_ID = 2;

    const _OLD_PREMIUM_PLAN_ID = 2;
    const _NEW_PREMIUM_PLAN_ID = 4;

    const _OLD_VIP_PLAN_ID = 3;
    const _NEW_VIP_PLAN_ID = 5;

    /**
     * first/old premium channels will be set manually
     */
    const _OLD_PREMIUM_2017_PLAN_ID = 3;

    const _OLD_PLANS_NEW_PLANS = [
        self::_OLD_FREE_PLAN_ID => self::_NEW_FREE_PLAN_ID,
        self::_OLD_EARLY_PLAN_ID => self::_NEW_EARLY_PLAN_ID,
        self::_OLD_PREMIUM_PLAN_ID => self::_NEW_PREMIUM_PLAN_ID,
        self::_OLD_VIP_PLAN_ID => self::_NEW_VIP_PLAN_ID,
    ];

    const _OLD_PREMIUM_CHANNEL_IDS = [
        'UCnF1gaTK11ax2pWCIdUp8-w', // | Delphine Dimanche => 6€/month                                                      
        'UCnf8HI3gUteF1BKAvrDO9dQ', // | Alex Borto 66€/year
        'UCNHFiyWgsnaSOsMtSoV_Q1A', // | Axiome => 6€/month
        'UCq80IvL314jsE7PgYsTdw7Q', // | Accropolis Replays => 6€/month
        //'UCSMzy1n4Arqk_hCCOYOQn9g', // | iRunFarMedia => 29$/year
        'UCU_gPhU-eAI56oUeFzVyUUQ', // | WP Marmite => 66€/year
    ];
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
            ->where('active',1)
            ->get();

        Subscription::truncate();

        /**
         * for each channel add a subscription according to its channel_premium state
         */
        foreach ($channels as $channel){

            /**
             * getting new plan according to old plan
             */
            $newPlanId = self::_OLD_PLANS_NEW_PLANS[$channel->channel_premium];

            /**
             * for some specific old premium plan we are setting a specific 2017 plan
             */
            if (in_array($channel->channel_id, self::_OLD_PREMIUM_CHANNEL_IDS)) {
                $newPlanId = self::_OLD_PREMIUM_2017_PLAN_ID;
            }

            /**
             * Insert one subscription with one plan by channel
             * for free channels plan start when channel is created
             * for early channels, the same
             * for premium we are setting today 
             */
            Subscription::insert([
                'channel_id'        => $channel->channel_id,
                'plan_id'           => $newPlanId,                
                'trial_ends_at'     => null,
                'ends_at'           => $channel->active==1 ? null : $channel->channel_updatedAt, // when set to active=0, channels are no more updated
                'created_at'        => $channel->channel_createdAt,
                'updated_at'        => Carbon::now(),
            ]);
        }

        /**
         * Once subscriptions are set we have to change date for current customers
         */
        

    }
}
