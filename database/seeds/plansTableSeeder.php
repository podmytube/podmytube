<?php

use App\Plan;
Use Carbon\Carbon;

use Illuminate\Database\Seeder;

class plansTableSeeder extends Seeder
{

    const max_episodes_by_plan = [
        'free' => 2,
        'standard_premium' => 10,
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
        /**
         * creating old plans
         * define('_CHANNEL_FREE', 0);
         * define('_CHANNEL_EARLY_BIRD', 1);
         * define('_CHANNEL_PREMIUM', 2);
         * define('_CHANNEL_VIP', 3);
         */

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Plan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        /**
         * forever free 
         */
        Plan::insert([
			'name'              => 'forever_free',
            'price_per_month'   => 0,
            'nb_episodes_per_month' => self::max_episodes_by_plan['free'],
            'created_at'        => Carbon::createFromDate(2017,1,1),
            'updated_at'        => Carbon::now(),
        ]);
        
        /**
         * 2017 
         */
        Plan::insert([
			'name'              => 'early_bird_2017',
            'price_per_month'   => 0,
            'nb_episodes_per_month' => self::max_episodes_by_plan['vip_premium'],
            'created_at'        => Carbon::createFromDate(2017,1,1),
            'updated_at'        => Carbon::now(),
		]);

        Plan::insert([
			'name'              => 'premium_2017',
            'price_per_month'   => 6,
            'nb_episodes_per_month' => self::max_episodes_by_plan['standard_premium'],
            'created_at'        => Carbon::createFromDate(2017,1,1),
            'updated_at'        => Carbon::now(),
        ]);
        
        
        /** 
         * September 2018
         */
        Plan::insert([
			'name'              => 'weekly_youtuber_sept_2018',
            'price_per_month'   => 9,
            'nb_episodes_per_month' => self::max_episodes_by_plan['standard_premium'],
            'created_at'        => Carbon::createFromDate(2018,9,1),
            'updated_at'        => Carbon::now(),
        ]);

        Plan::insert([
			'name'              => 'daily_youtuber_sept_2018',
            'price_per_month'   => 29,
            'nb_episodes_per_month' => self::max_episodes_by_plan['vip_premium'],
            'created_at'        => Carbon::createFromDate(2018,9,1),
            'updated_at'        => Carbon::now(),
        ]);        

    }
}
