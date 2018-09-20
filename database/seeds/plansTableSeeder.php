<?php

use App\Plan;
Use Carbon\Carbon;

use Illuminate\Database\Seeder;

class plansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * creating old plans
         * define('_CHANNEL_FREE', 0);
         * define('_CHANNEL_EARLY_BIRD', 1);
         * define('_CHANNEL_PREMIUM', 2);
         * define('_CHANNEL_VIP', 3);
         */
        Plan::truncate();

        /**
         * 2017 
         */
        Plan::insert([
			'id'                => 1,
			'name'              => 'early_bird_2017',
            'price_per_month'   => 0,
            'created_at'        => Carbon::createFromDate(2017,1,1),
            'updated_at'        => Carbon::now(),
		]);

        Plan::insert([
			'id'                => 2,
			'name'              => 'premium_2017',
            'price_per_month'   => 6,
            'created_at'        => Carbon::createFromDate(2017,1,1),
            'updated_at'        => Carbon::now(),
        ]);
        
        /** 
         * September 2018
         */
        Plan::insert([
			'id'                => 3,
			'name'              => 'weekly_youtuber_sept_2018',
            'price_per_month'   => 9,
            'created_at'        => Carbon::createFromDate(2018,9,1),
            'updated_at'        => Carbon::now(),
        ]);

        Plan::insert([
			'id'                => 4,
			'name'              => 'daily_youtuber_sept_2018',
            'price_per_month'   => 29,
            'created_at'        => Carbon::createFromDate(2018,9,1),
            'updated_at'        => Carbon::now(),
		]);
    }
}
