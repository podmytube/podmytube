<?php

use App\Plan;
Use Carbon\Carbon;

use Illuminate\Database\Seeder;

class plansTableSeeder extends Seeder
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Plan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        /**
         * forever free 
         */
        Plan::insert([
            'id'                => 1,
			'name'              => 'forever_free',
            'price'             => 0,
            'nb_episodes_per_month' => self::max_episodes_by_plan['free'],
            'created_at'        => Carbon::createFromDate(2017,1,1),
            'updated_at'        => Carbon::now(),
        ]);
        
        /**
         * 2017 
         */
        Plan::insert([
            'id'                => 2,
            'name'              => 'early_bird_2017',
            'price'             => 0,
            'nb_episodes_per_month' => self::max_episodes_by_plan['vip_premium'],
            'created_at'        => Carbon::createFromDate(2017,1,1),
            'updated_at'        => Carbon::now(),
		]);

        /**
         * first premium subscribers --- monthly
         */
        Plan::insert([
            'id'                => 3,
            'name'              => 'promo_monthly',
            'price'             => 6,
            'nb_episodes_per_month' => self::max_episodes_by_plan['standard_premium'],
            'created_at'        => Carbon::createFromDate(2017,1,1),
            'updated_at'        => Carbon::now(),
        ]);

        /**
         * first premium subscribers --- yearly
         */
        Plan::insert([
            'id'                => 4,
            'name'              => 'promo_yearly',
            'price'             => 66,
            'billing_yearly'    => true,
            'nb_episodes_per_month' => self::max_episodes_by_plan['standard_premium'],
            'created_at'        => Carbon::createFromDate(2017,1,1),
            'updated_at'        => Carbon::now(),
        ]);
        
        
        /** 
         * September 2018
         */
        Plan::insert([
            'id'                => 5,
            'name'              => 'weekly_youtuber',
            'price'             => 9,
            'nb_episodes_per_month' => self::max_episodes_by_plan['standard_premium'],
            'created_at'        => Carbon::createFromDate(2018,9,1),
            'updated_at'        => Carbon::now(),
        ]);

        Plan::insert([
            'id'                => 6,
            'name'              => 'daily_youtuber',
            'price'             => 29,
            'nb_episodes_per_month' => self::max_episodes_by_plan['vip_premium'],
            'created_at'        => Carbon::createFromDate(2018,9,1),
            'updated_at'        => Carbon::now(),
        ]);        

        /**
         * Accropolis wart
         */
        Plan::insert([
            'name'              => 'accropolis_6_euros',
            'price'             => 7,
            'nb_episodes_per_month' => self::max_episodes_by_plan['accropolis'],
            'created_at'        => Carbon::createFromDate(2017,1,1),
            'updated_at'        => Carbon::now(),
        ]);

    }
}
