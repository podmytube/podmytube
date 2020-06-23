<?php

use App\Plan;
use App\StripePlan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StripePlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stripe_plans')->delete();
        /**
         * ======================================
         * promo monthly
         * ======================================
         * test
         */
        StripePlan::insert([
            'plan_id' => Plan::PROMO_MONTHLY_PLAN_ID,
            'stripe_id' => StripePlan::PROMO_MONTHLY_PLAN_TEST,
            'is_live' => 0,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);
        /**
         * prod
         */
        StripePlan::insert([
            'plan_id' => Plan::PROMO_MONTHLY_PLAN_ID,
            'stripe_id' => StripePlan::PROMO_MONTHLY_PLAN_PROD,
            'is_live' => 1,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * ======================================
         * promo yearly
         * ======================================
         * test
         */
        StripePlan::insert([
            'plan_id' => Plan::PROMO_YEARLY_PLAN_ID,
            'stripe_id' => StripePlan::PROMO_YEARLY_PLAN_TEST,
            'is_live' => 0,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);
        /**
         * prod
         */
        StripePlan::insert([
            'plan_id' => Plan::PROMO_YEARLY_PLAN_ID,
            'stripe_id' => StripePlan::PROMO_YEARLY_PLAN_PROD,
            'is_live' => 1,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * ======================================
         * weekly monthly
         * ======================================
         * test
         */

        StripePlan::insert([
            'plan_id' => Plan::WEEKLY_PLAN_ID,
            'stripe_id' => StripePlan::WEEKLY_PLAN_TEST,
            'is_live' => 0,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);
        /**
         * prod
         */
        StripePlan::insert([
            'plan_id' => Plan::WEEKLY_PLAN_ID,
            'stripe_id' => StripePlan::WEEKLY_PLAN_PROD,
            'is_live' => 1,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * ======================================
         * daily monthly
         * ======================================
         * test
         */

        StripePlan::insert([
            'plan_id' => Plan::DAILY_PLAN_ID,
            'stripe_id' => StripePlan::DAILY_PLAN_TEST,
            'is_live' => 0,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);
        /**
         * prod
         */
        StripePlan::insert([
            'plan_id' => Plan::DAILY_PLAN_ID,
            'stripe_id' => StripePlan::DAILY_PLAN_PROD,
            'is_live' => 1,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * ======================================
         * accropolis only
         * ======================================
         * test
         */

        StripePlan::insert([
            'plan_id' => Plan::ACCROPOLIS_PLAN_ID,
            'stripe_id' => StripePlan::ACCROPOLIS_PLAN_TEST,
            'is_live' => 0,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);
        /**
         * prod
         */
        StripePlan::insert([
            'plan_id' => Plan::ACCROPOLIS_PLAN_ID,
            'stripe_id' => StripePlan::ACCROPOLIS_PLAN_PROD,
            'is_live' => 1,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * ======================================
         * weekly monthly (PROMO)
         * ======================================
         * test
         */

        StripePlan::insert([
            'plan_id' => Plan::WEEKLY_PLAN_PROMO_ID,
            'stripe_id' => StripePlan::PROMO_WEEKLY_PLAN_TEST,
            'is_live' => 0,
            'created_at' => Carbon::createFromDate(2020, 6, 14),
            'updated_at' => Carbon::now(),
        ]);
        /**
         * prod
         */
        StripePlan::insert([
            'plan_id' => Plan::WEEKLY_PLAN_PROMO_ID,
            'stripe_id' => StripePlan::PROMO_WEEKLY_PLAN_PROD,
            'is_live' => 1,
            'created_at' => Carbon::createFromDate(2020, 6, 14),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * ======================================
         * daily monthly (PROMO)
         * ======================================
         * test
         */

        StripePlan::insert([
            'plan_id' => Plan::DAILY_PLAN_PROMO_ID,
            'stripe_id' => StripePlan::PROMO_DAILY_PLAN_TEST,
            'is_live' => 0,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);
        /**
         * prod
         */
        StripePlan::insert([
            'plan_id' => Plan::DAILY_PLAN_PROMO_ID,
            'stripe_id' => StripePlan::PROMO_DAILY_PLAN_PROD,
            'is_live' => 1,
            'created_at' => Carbon::createFromDate(2019, 3, 10),
            'updated_at' => Carbon::now(),
        ]);
    }
}
