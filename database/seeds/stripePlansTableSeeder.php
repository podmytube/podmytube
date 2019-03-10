<?php

use Illuminate\Database\Seeder;
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
         * forever free
         */
        stripePlans::insert(
            
            // promo_monthly plan_EfYDgsuNMdj8Sb	plan_EcuGg9SyUBw97i
            [
                'plan_id' => 3,
                'stripe_id' => 'plan_EfYDgsuNMdj8Sb',
                'is_live' => 0,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ],
            [
                'plan_id' => 3,
                'stripe_id' => 'plan_EcuGg9SyUBw97i',
                'is_live' => 1,
                'created_at' => Carbon::createFromDate(2019, 3, 10),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}
