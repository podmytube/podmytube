<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * These seeders are used every time
         */
        Model::unguard();

        $this->call([
            UsersTableSeeder::class,
            PlansTableSeeder::class,
            CategoriesTableSeeder::class,
            StripePlansTableSeeder::class,
            ApiKeysTableSeeder::class,
        ]);

        /**
         * this one should not be used in testing mode.
         * In my tests I'm using my personal channel, 
         * this seeder is doing same, 
         */
        if (!App::environment('testing')) {
            $this->call([
                ChannelsTableSeeder::class,
            ]);
        }

        Model::reguard();
    }
}
