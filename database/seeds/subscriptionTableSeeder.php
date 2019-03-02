<?php

use App\Channel;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class subscriptionTableSeeder extends Seeder
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
    const _NEW_VIP_PLAN_ID = 5;
    const _OLD_MONTHLY_AT_29 = [
        'UCSMzy1n4Arqk_hCCOYOQn9g', // | iRunFarMedia => 29$/month
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
            ->where('active', 1)
            ->get();

        Subscription::truncate();

        /**
         * for each channel add a subscription according to its channel_premium state
         */
        foreach ($channels as $channel) {

            if ($channel->channel_premium == 1) {
                /**
                 * if channel_premium == 1 become early_bird_2017 2
                 */
                $newPlanId = self::_EARLY_PLAN_ID;
            } elseif (in_array($channel->channel_id, self::_OLD_MONTHLY_AT_6)) {
                /**
                 * 6€/monthly migration
                 */
                $newPlanId = self::_PREMIUM_2017_MONTHLY_PLAN_ID;

            } elseif (in_array($channel->channel_id, self::_OLD_YEARLY_AT_66)) {
                /**
                 * 66€/yearly migration
                 */
                $newPlanId = self::_PREMIUM_2017_YEARLY_PLAN_ID;
            } elseif (in_array($channel->channel_id, self::_OLD_MONTHLY_AT_29)) {
                /**
                 * 29€/monthly migration
                 */
                $newPlanId = self::_NEW_VIP_PLAN_ID;
            } else {
                // no subscription for free plan
                continue;
            }

            /**
             * Accropolis was set to channel_premium=3 once upon a time to increase
             * nb of episodes generated
             */
            if ($channel->channel_id == "UCq80IvL314jsE7PgYsTdw7Q") {
                $newPlanId = self::_ACCROPOLIS_PLAN_ID;
            }

            /**
             * Insert one subscription with one plan by channel
             * for all channels plan start when channel is created
             * for early channels, the same
             * for yearly channels I will set end_at manually in many months/weeks
             */
            Subscription::insert([
                'channel_id' => $channel->channel_id,
                'plan_id' => $newPlanId,
                'trial_ends_at' => null,
                'ends_at' => $channel->active == 1 ? null : $channel->channel_updatedAt, // when set to active=0, channels are no more updated
                'created_at' => $channel->channel_createdAt,
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
