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

        $data = [
            /**
             * forever free
             */
            [
                'name' => 'Forever free',
                'slug' => 'forever_free',
                'price' => 0,
                'billing_yearly' => false,
                'nb_episodes_per_month' => self::max_episodes_by_plan['free'],
                'created_at' => Carbon::createFromDate(2017, 1, 1),
                'updated_at' => Carbon::now(),
            ],

            /**
             * 2017
             */
            [
                'name' => 'Early bird',
                'slug' => 'early_bird',
                'price' => 0,
                'billing_yearly' => false,
                'nb_episodes_per_month' => self::max_episodes_by_plan['vip_premium'],
                'created_at' => Carbon::createFromDate(2017, 1, 1),
                'updated_at' => Carbon::now(),
            ],

            /**
             * first premium subscribers --- monthly
             */
            [
                'name' => 'Promo 6 (monthly)',
                'slug' => 'monthly_6',
                'price' => 6,
                'billing_yearly' => false,
                'nb_episodes_per_month' => self::max_episodes_by_plan['standard_premium'],
                'created_at' => Carbon::createFromDate(2017, 1, 1),
                'updated_at' => Carbon::now(),
            ],

            /**
             * first premium subscribers --- yearly
             */
            [
                'name' => 'Promo 66 (yearly)',
                'slug' => 'yearly_66',
                'price' => 66,
                'billing_yearly' => true,
                'nb_episodes_per_month' => self::max_episodes_by_plan['standard_premium'],
                'created_at' => Carbon::createFromDate(2017, 1, 1),
                'updated_at' => Carbon::now(),
            ],

            /**
             * September 2018
             */
            [
                'name' => 'Weekly Youtuber',
                'slug' => 'weekly_youtuber',
                'price' => 9,
                'billing_yearly' => false,
                'nb_episodes_per_month' => self::max_episodes_by_plan['standard_premium'],
                'created_at' => Carbon::createFromDate(2018, 9, 1),
                'updated_at' => Carbon::now(),
            ],

            [
                'name' => 'Daily Youtuber',
                'slug' => 'daily_youtuber',
                'price' => 29,
                'billing_yearly' => false,
                'nb_episodes_per_month' => self::max_episodes_by_plan['vip_premium'],
                'created_at' => Carbon::createFromDate(2018, 9, 1),
                'updated_at' => Carbon::now(),
            ],

            /**
             * Accropolis wart
             */
            [
                'name' => 'Spécial accropolis',
                'slug' => 'accropolis_6_euros',
                'price' => 6,
                'billing_yearly' => false,
                'nb_episodes_per_month' => self::max_episodes_by_plan['accropolis'],
                'created_at' => Carbon::createFromDate(2017, 1, 1),
                'updated_at' => Carbon::now(),
            ],
        ];

        $index = 1;
        $data = array_map(
            function ($item) use (&$index) {
                return array_merge($item, [
                    'id' => $index++,
                ]);
            },
            $data
        );

        Plan::insert($data);
    }
}
