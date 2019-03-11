<?php

use Illuminate\Database\Seeder;
use App\Plan;
use App\stripePlans;
use Carbon\Carbon;

class stripePlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        stripePlans::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        /**
         * ======================================
         * promo monhtly
         * ======================================
         * test
         */
        stripePlans::insert(            
            [
                'plan_id' => Plan::_PROMO_MONTHLY_PLAN_ID,
                'stripe_id' => 'plan_EfYDgsuNMdj8Sb',
                'is_live' => 0,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]);
        /**
         * prod
         */
        stripePlans::insert(
            [
                'plan_id' => Plan::_PROMO_MONTHLY_PLAN_ID,
                'stripe_id' => 'plan_EcuGg9SyUBw97i',
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
        stripePlans::insert(            
            [
                'plan_id' => Plan::_PROMO_YEARLY_PLAN_ID,
                'stripe_id' => 'plan_EfYBFztmlQ3u4C',
                'is_live' => 0,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]);
        /**
         * prod
         */
        stripePlans::insert(
            [
                'plan_id' => Plan::_PROMO_YEARLY_PLAN_ID,
                'stripe_id' => 'plan_EcuJ2npV5EMrCg',
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
        stripePlans::insert(            
            [
                'plan_id' => Plan::_WEEKLY_PLAN_ID,
                'stripe_id' => 'plan_EfudBu6TCXHWEg',
                'is_live' => 0,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]);
        /**
         * prod
         */
        stripePlans::insert(
            [
                'plan_id' => Plan::_WEEKLY_PLAN_ID,
                'stripe_id' => 'plan_EaIv2XTMGtuY5g',
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
        stripePlans::insert(            
            [
                'plan_id' => Plan::_DAILY_PLAN_ID,
                'stripe_id' => 'plan_EfuceKVUwJTt5O',
                'is_live' => 0,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]);
        /**
         * prod
         */
        stripePlans::insert(
            [
                'plan_id' => Plan::_DAILY_PLAN_ID,
                'stripe_id' => 'plan_DFsB9U76WaSaR3',
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
        	     	
        stripePlans::insert(            
            [
                'plan_id' => Plan::_ACCROPOLIS_PLAN_ID,
                'stripe_id' => 'plan_EfubS6xkc5amyO',
                'is_live' => 0,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]);
        /**
         * prod
         */
        stripePlans::insert(
            [
                'plan_id' => Plan::_ACCROPOLIS_PLAN_ID,
                'stripe_id' => 'plan_Ecv3k67W6rsSKk',
                'is_live' => 1,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}
