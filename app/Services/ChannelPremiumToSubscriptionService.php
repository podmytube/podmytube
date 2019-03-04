<?php
/**
 * This class will be used (for a short time) to convert old
 * channel premium registration to subscription model registration
 */
namespace App\Services;

use App\Channel;
use App\Exceptions\FreePlanDoNotNeedSubscriptionException;
use App\Plan;
use App\Subscription;
use Carbon\Carbon;

/**
 * This class will be used (for a short time) to convert old
 * channel premium registration to subscription model registration
 */
class ChannelPremiumToSubscriptionService
{
    /**
     * Free plan
     * channel_premium=0 => plans.id=1
     */
    const _FREE_PLAN_ID = 1;

    /**
     * Early bird
     * channel_premium=1 => plans.id=2
     */
    const _EARLY_PLAN_ID = 2;

    /**
     * Weekly youtuber - weekly youtuber 9€ / month
     */
    const _WEEKLY_PLAN_ID = 5;

    /**
     * Daily youtuber - daily youtuber 29€ / month     
     */
    const _DAILY_PLAN_ID = 6;

    /**
     * First premium users (6€) monthly
     * 'UCnF1gaTK11ax2pWCIdUp8-w', // | Delphine Dimanche => 6€/month
     */
    const _PREMIUM_2017_MONTHLY_PLAN_ID = 3;
    const _OLD_MONTHLY_AT_6 = [
        'UCnF1gaTK11ax2pWCIdUp8-w', // Delphine Dimanche => 6€/month
    ];

    /**
     * Accropolis is a specific case. They are paying only 6€ and the nb
     * of episodes converted / month was not limited before
     * 'UCq80IvL314jsE7PgYsTdw7Q', // | Accropolis Replays => 6€/month
     */
    const _ACCROPOLIS_PLAN_ID = 7;

    /**
     * First premium users yearly plan (66€) channel_premium
     * 'UCnf8HI3gUteF1BKAvrDO9dQ', // | Alex Borto 66€/year
     * 'UCU_gPhU-eAI56oUeFzVyUUQ', // | WP Marmite => 66€/year
     * 'UCNHFiyWgsnaSOsMtSoV_Q1A', // | Axiome => 66€/year
     */
    const _PREMIUM_2017_YEARLY_PLAN_ID = 4;
    const _OLD_YEARLY_AT_66 = [
        'UCnf8HI3gUteF1BKAvrDO9dQ', // | Alex Borto 66€/year
        'UCU_gPhU-eAI56oUeFzVyUUQ', // | WP Marmite => 66€/year
        'UCNHFiyWgsnaSOsMtSoV_Q1A', // | Axiome => 66€/year
    ];

    /**
     * Daily youtuber - daily youtuber 29€ / month
     * 'UCSMzy1n4Arqk_hCCOYOQn9g', // | iRunFarMedia => 29$/month
     */    
    const _OLD_MONTHLY_AT_29 = [
        'UCSMzy1n4Arqk_hCCOYOQn9g', // | iRunFarMedia => 29$/month
    ];

    /**
     * This function will create one subscription model for one channel according to its channel_premium.
     *
     * @param object App\Channel model $channel the channel to convert
     * @return void
     */
    public static function transform(Channel $channel)
    {

        try {
            /**
             * Get future plan for this channel
             */
            $newPlanId = self::getPlanIdForChannel($channel);

            /**
             * Insert one subscription with one plan by channel
             * for all channels plan start when channel is created
             * for early channels, the same
             * for yearly channels I will set end_at manually in many months/weeks
             */
            $firstDayOfThisMonth = new Carbon('first day of this month');

            $plan = Plan::find($newPlanId);
            /**
             * if plan has monthly billing we are fixing the end of subscription to first day of next month
             * if billed yearly we are fixing it to first day of this month + 1 year
             */
            $endsAt=null;
            if ($newPlanId != self::_EARLY_PLAN_ID) {
                $endsAt = new Carbon('first day of next month');
                if($plan->billing_yearly==1){
                    $endsAt = $firstDayOfThisMonth->copy()->addYear();                
                }                
            }
            

            Subscription::insert([
                'channel_id' => $channel->channel_id,
                'plan_id' => $newPlanId,
                'trial_ends_at' => null,
                'ends_at' => isset($endsAt) ? $endsAt->toDateTimeString() : null,
                'created_at' => isset($channel->channel_createdAt) ? $channel->channel_createdAt : $firstDayOfThisMonth->toDateTimeString(),
                'updated_at' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    /**
     * This function will return the next plan id for the channel specified.
     * @param object App\Channel $channel model object
     * @return integer newPlanId
     */
    public static function getPlanIdForChannel(Channel $channel)
    {
        /**
         * Free users
         */
        if ($channel->channel_premium == 0) {
            // no subscription for free plan
            throw new FreePlanDoNotNeedSubscriptionException("Channel {{$channel->channel_id}} has a free plan and does not need subscription.");
        } 
        
        /**
         * Accropolis was set to channel_premium=3 once upon a time to increase
         * nb of episodes generated
         */
        if ($channel->channel_id == "UCq80IvL314jsE7PgYsTdw7Q") {
            return self::_ACCROPOLIS_PLAN_ID;
        }

        /**
         * Specific case
         */
        if (in_array($channel->channel_id, self::_OLD_MONTHLY_AT_6)) {
            /**
             * 6€/monthly migration
             */
            return self::_PREMIUM_2017_MONTHLY_PLAN_ID;

        } elseif (in_array($channel->channel_id, self::_OLD_YEARLY_AT_66)) {
            /**
             * 66€/yearly migration
             */
            return self::_PREMIUM_2017_YEARLY_PLAN_ID;
        } elseif (in_array($channel->channel_id, self::_OLD_MONTHLY_AT_29)) {
            /**
             * 29€/monthly migration
             */
            return self::_DAILY_PLAN_ID;
        }
        
        switch ($channel->channel_premium) {
            case 1: $newPlanId = self::_EARLY_PLAN_ID; break;    
            case 2: $newPlanId = self::_WEEKLY_PLAN_ID; break;    
            case 3: $newPlanId = self::_DAILY_PLAN_ID; break;
        }
        

        

        return $newPlanId;
    }
}
