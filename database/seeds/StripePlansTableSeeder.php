<?php

use App\Plan;
use App\StripePlan;
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

        $freePlan = Plan::bySlug('forever_free');
        $earlyPlan = Plan::bySlug('early_bird');
        $promoPlan = Plan::bySlug('promo');
        $weeklyPlan9 = Plan::bySlug('weekly_youtuber');
        $dailyPlan29 = Plan::bySlug('daily_youtuber');
        $starterPlan = Plan::bySlug('starter');
        $professionalPlan = Plan::bySlug('professional');
        $businessPlan = Plan::bySlug('business');

        /**
         * ======================================
         * promo
         * ======================================
         */
        /** monthly */
        StripePlan::insert([
            'plan_id' => $promoPlan->id,
            'stripe_live_id' => 'price_1Gu1YPLrQ8vSqYZERxvBFAgu',
            'stripe_test_id' => '',
            'is_yearly' => false,
        ]);

        /** yearly */
        StripePlan::insert([
            'plan_id' => $promoPlan->id,
            'stripe_live_id' => 'plan_EcuJ2npV5EMrCg',
            'stripe_test_id' => '',
            'is_yearly' => true,
        ]);

        /**
         * ======================================
         * weekly
         * ======================================
         */

        /** monthly */
        StripePlan::insert([
            'plan_id' => $weeklyPlan9->id,
            'stripe_live_id' => 'price_1Gu1YPLrQ8vSqYZERxvBFAgu',
            'stripe_test_id' => '',
            'is_yearly' => false,
        ]);

        /**
         * ======================================
         * daily
         * ======================================
         */

        /** monthly */
        StripePlan::insert([
            'plan_id' => $dailyPlan29->id,
            'stripe_live_id' => 'plan_DFsB9U76WaSaR3',
            'stripe_test_id' => '',
            'is_yearly' => false,
        ]);

        /**
         * ======================================
         * starter
         * ======================================
         */
        /** yearly */
        StripePlan::insert([
            'plan_id' => $starterPlan->id,
            'stripe_live_id' => 'price_1HmxVLLrQ8vSqYZEFlv2SUpd',
            'stripe_test_id' => '',
            'is_yearly' => true,
        ]);

        /** monthly */
        StripePlan::insert([
            'plan_id' => $starterPlan->id,
            'stripe_live_id' => 'price_1HmxVLLrQ8vSqYZEOK2BxHfy',
            'stripe_test_id' => '',
            'is_yearly' => false,
        ]);

        /**
         * ======================================
         * professional
         * ======================================
         */
        /** yearly */
        StripePlan::insert([
            'plan_id' => $professionalPlan->id,
            'stripe_live_id' => 'price_1Hmxa6LrQ8vSqYZEGmgc6MXn',
            'stripe_test_id' => '',
            'is_yearly' => true,
        ]);

        /** monthly */
        StripePlan::insert([
            'plan_id' => $professionalPlan->id,
            'stripe_live_id' => 'price_1Hmxa7LrQ8vSqYZErLv9rG9m',
            'stripe_test_id' => '',
            'is_yearly' => false,
        ]);

        /**
         * ======================================
         * business
         * ======================================
         */
        /** yearly */
        StripePlan::insert([
            'plan_id' => $businessPlan->id,
            'stripe_live_id' => 'price_1HmxbYLrQ8vSqYZEdab8H6WN',
            'stripe_test_id' => '',
            'is_yearly' => true,
        ]);

        /** monthly */
        StripePlan::insert([
            'plan_id' => $businessPlan->id,
            'stripe_live_id' => 'price_1HmxbYLrQ8vSqYZE1Q3qOMt1',
            'stripe_test_id' => '',
            'is_yearly' => false,
        ]);
    }
}
