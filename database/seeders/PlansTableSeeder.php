<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Plan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('plans')->truncate();
        Schema::enableForeignKeyConstraints();

        $data = [
            // forever free
            [
                'name' => 'Forever free',
                'slug' => 'forever_free',
                'price' => 0,
                'nb_episodes_per_month' => 1,
                'created_at' => Carbon::createFromDate(2017, 1, 1),
                'updated_at' => Carbon::now(),
            ],

            // 2017
            [
                'name' => 'Early bird',
                'slug' => 'early_bird',
                'price' => 0,
                'nb_episodes_per_month' => 30,
                'created_at' => Carbon::createFromDate(2017, 1, 1),
                'updated_at' => Carbon::now(),
            ],

            // first premium subscribers --- monthly
            [
                'name' => 'Promo',
                'slug' => 'monthly_6',
                'price' => 6,
                'nb_episodes_per_month' => 5,
                'created_at' => Carbon::createFromDate(2017, 1, 1),
                'updated_at' => Carbon::now(),
            ],

            // September 2018
            [
                'name' => 'Weekly Youtuber',
                'slug' => 'weekly_youtuber',
                'price' => 9,
                'nb_episodes_per_month' => 10,
                'created_at' => Carbon::createFromDate(2018, 9, 1),
            ],

            [
                'name' => 'Daily Youtuber',
                'slug' => 'daily_youtuber',
                'price' => 29,
                'nb_episodes_per_month' => 33,
                'created_at' => Carbon::createFromDate(2018, 9, 1),
            ],

            /*
             * March 2021
             * yearly price is monthly price x10
             */
            [
                'name' => 'Starter',
                'price' => 9,
                'nb_episodes_per_month' => 5,
                'created_at' => Carbon::createFromDate(2021, 3, 22),
            ],

            [
                'name' => 'Professional',
                'price' => 29,
                'nb_episodes_per_month' => 12,
                'created_at' => Carbon::createFromDate(2021, 3, 22),
            ],

            [
                'name' => 'Business',
                'price' => 79,
                'nb_episodes_per_month' => 33,
                'created_at' => Carbon::createFromDate(2021, 3, 22),
            ],
        ];

        $index = 1;
        $data = array_map(
            function ($item) use (&$index) {
                return array_merge($item, [
                    'id' => $index++,
                    'slug' => $item['slug'] ?? Str::slug($item['name']),
                    'updated_at' => Carbon::now(),
                ]);
            },
            $data
        );

        Plan::insert($data);
    }
}
