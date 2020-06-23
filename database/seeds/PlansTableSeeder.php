<?php

use App\Plan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    const max_episodes_by_plan = [
        'free' => 2,
        'standard_premium' => 10,
        'accropolis' => 20, // to be removed ... when they will upgrade or leave
        'vip_premium' => 33,
        'early' => 33,
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->delete();

        /**
         * forever free
         */
        Plan::insert([
            'id' => Plan::FREE_PLAN_ID,
            'name' => 'forever_free',
            'price' => 0,
            'nb_episodes_per_month' => self::max_episodes_by_plan['free'],
            'created_at' => Carbon::createFromDate(2017, 1, 1),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * 2017
         */
        Plan::insert([
            'id' => Plan::EARLY_PLAN_ID,
            'name' => 'early_bird_2017',
            'price' => 0,
            'nb_episodes_per_month' =>
                self::max_episodes_by_plan['vip_premium'],
            'created_at' => Carbon::createFromDate(2017, 1, 1),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * first premium subscribers --- monthly
         */
        Plan::insert([
            'id' => Plan::PROMO_MONTHLY_PLAN_ID,
            'name' => 'promo_monthly',
            'price' => 6,
            'nb_episodes_per_month' =>
                self::max_episodes_by_plan['standard_premium'],
            'created_at' => Carbon::createFromDate(2017, 1, 1),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * first premium subscribers --- yearly
         */
        Plan::insert([
            'id' => Plan::PROMO_YEARLY_PLAN_ID,
            'name' => 'promo_yearly',
            'price' => 66,
            'billing_yearly' => true,
            'nb_episodes_per_month' =>
                self::max_episodes_by_plan['standard_premium'],
            'created_at' => Carbon::createFromDate(2017, 1, 1),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * September 2018
         */
        Plan::insert([
            'id' => Plan::WEEKLY_PLAN_ID,
            'name' => 'weekly_youtuber',
            'price' => 9,
            'nb_episodes_per_month' =>
                self::max_episodes_by_plan['standard_premium'],
            'created_at' => Carbon::createFromDate(2018, 9, 1),
            'updated_at' => Carbon::now(),
        ]);

        Plan::insert([
            'id' => Plan::DAILY_PLAN_ID,
            'name' => 'daily_youtuber',
            'price' => 29,
            'nb_episodes_per_month' =>
                self::max_episodes_by_plan['vip_premium'],
            'created_at' => Carbon::createFromDate(2018, 9, 1),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * Accropolis wart
         */
        Plan::insert([
            'id' => Plan::ACCROPOLIS_PLAN_ID,
            'name' => 'accropolis_6_euros',
            'price' => 6,
            'nb_episodes_per_month' => self::max_episodes_by_plan['accropolis'],
            'created_at' => Carbon::createFromDate(2017, 1, 1),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * June 2020
         */
        Plan::insert([
            'id' => Plan::WEEKLY_PLAN_PROMO_ID,
            'name' => 'weekly_youtuber_promo',
            'price' => 6,
            'nb_episodes_per_month' =>
                self::max_episodes_by_plan['standard_premium'],
            'created_at' => Carbon::createFromDate(2020, 6, 14),
            'updated_at' => Carbon::now(),
        ]);

        Plan::insert([
            'id' => Plan::DAILY_PLAN_PROMO_ID,
            'name' => 'daily_youtuber_promo',
            'price' => 25,
            'nb_episodes_per_month' =>
                self::max_episodes_by_plan['vip_premium'],
            'created_at' => Carbon::createFromDate(2020, 6, 14),
            'updated_at' => Carbon::now(),
        ]);
    }
}
