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
        StripePlan::insert(            
            [
                'plan_id' => Plan::_PROMO_MONTHLY_PLAN_ID,
                'stripe_id' => StripePlan::_PROMO_MONTHLY_PLAN_TEST,
                'is_live' => 0,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]);
        /**
         * prod
         */
        StripePlan::insert(
            [
                'plan_id' => Plan::_PROMO_MONTHLY_PLAN_ID,
                'stripe_id' => StripePlan::_PROMO_MONTHLY_PLAN_PROD,
                'is_live' => 1,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]
        );

        /**
         * ======================================
         * promo yearly
         * ======================================
         * test
         */
        StripePlan::insert(            
            [
                'plan_id' => Plan::_PROMO_YEARLY_PLAN_ID,
                'stripe_id' => StripePlan::_PROMO_YEARLY_PLAN_TEST,
                'is_live' => 0,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]);
        /**
         * prod
         */
        StripePlan::insert(
            [
                'plan_id' => Plan::_PROMO_YEARLY_PLAN_ID,
                'stripe_id' => StripePlan::_PROMO_YEARLY_PLAN_PROD,
                'is_live' => 1,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]
        );

        /**
         * ======================================
         * weekly yearly
         * ======================================
         * test
         */        	
        StripePlan::insert(            
            [
                'plan_id' => Plan::_WEEKLY_PLAN_ID,
                'stripe_id' => StripePlan::_WEEKLY_PLAN_TEST,
                'is_live' => 0,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]);
        /**
         * prod
         */
        StripePlan::insert(
            [
                'plan_id' => Plan::_WEEKLY_PLAN_ID,
                'stripe_id' => StripePlan::_WEEKLY_PLAN_PROD,
                'is_live' => 1,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]
        );

        /**
         * ======================================
         * daily yearly
         * ======================================
         * test
         */        	
        StripePlan::insert(            
            [
                'plan_id' => Plan::_DAILY_PLAN_ID,
                'stripe_id' => StripePlan::_DAILY_PLAN_TEST,
                'is_live' => 0,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]);
        /**
         * prod
         */
        StripePlan::insert(
            [
                'plan_id' => Plan::_DAILY_PLAN_ID,
                'stripe_id' => StripePlan::_DAILY_PLAN_PROD,
                'is_live' => 1,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]
        );

        /**
         * ======================================
         * accropolis only
         * ======================================
         * test
         */   
        	     	
        StripePlan::insert(            
            [
                'plan_id' => Plan::_ACCROPOLIS_PLAN_ID,
                'stripe_id' => StripePlan::_ACCROPOLIS_PLAN_TEST,
                'is_live' => 0,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]);
        /**
         * prod
         */
        StripePlan::insert(
            [
                'plan_id' => Plan::_ACCROPOLIS_PLAN_ID,
                'stripe_id' => StripePlan::_ACCROPOLIS_PLAN_PROD,
                'is_live' => 1,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}
