<?php

use Carbon\Carbon;
use App\Subscription;
use App\Channel;

use Illuminate\Database\Seeder;

class subscriptionTableSeeder extends Seeder
{
    /**
     * Free plan
     * channel_premium=0 => plans.id=1
     */
    const _OLD_FREE_PLAN_ID = 0;
    const _NEW_FREE_PLAN_ID = 1;
    
    /**
     * Early bird
     * channel_premium=1 => plans.id=2
     */
    const _OLD_EARLY_PLAN_ID = 1;
    const _NEW_EARLY_PLAN_ID = 2;

    /**
     * First premium users (6€) channel_premium is 2 but some are at 6€/month 
     * and some other to 66€/year 
     * 'UCnF1gaTK11ax2pWCIdUp8-w', // | Delphine Dimanche => 6€/month                                                      
     * 'UCnf8HI3gUteF1BKAvrDO9dQ', // | Alex Borto 66€/year
     * 'UCNHFiyWgsnaSOsMtSoV_Q1A', // | Axiome => 66€/year
     * 'UCq80IvL314jsE7PgYsTdw7Q', // | Accropolis Replays => 6€/month
     * 'UCSMzy1n4Arqk_hCCOYOQn9g', // | iRunFarMedia => 29$/month
     * 'UCU_gPhU-eAI56oUeFzVyUUQ', // | WP Marmite => 66€/year
     */
    const _OLD_PREMIUM_2017_PLAN_ID = 2;
    const _NEW_PREMIUM_2017_PLAN_ID = 3;
    
    /**
     * premium plan - weekly youtuber 9€ / month
     */
    const _NEW_PREMIUM_PLAN_ID = 4;

    /**
     * Daily youtuber - daily youtuber 29€ / month
     */
    const _NEW_VIP_PLAN_ID = 5;
    
    /**
     * first/old premium channels will be set manually
     */
    

    const _OLD_PLANS_NEW_PLANS = [
        self::_OLD_FREE_PLAN_ID => self::_NEW_FREE_PLAN_ID,
        self::_OLD_EARLY_PLAN_ID => self::_NEW_EARLY_PLAN_ID,
        self::_OLD_PREMIUM_2017_PLAN_ID => self::_OLD_PREMIUM_2017_PLAN_ID,
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
             * Accropolis was set to channel_premium=3 once upon a time to increase 
             * nb of episodes generated
             */
            if ($channel->channel_id == "UCq80IvL314jsE7PgYsTdw7Q") {
                $newPlanId = self::_NEW_PREMIUM_PLAN_ID;
            }

            /**
             * Insert one subscription with one plan by channel
             * for all channels plan start when channel is created
             * for early channels, the same
             * for yearly channels I will set end_at manually in many months/weeks
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
    }
}
