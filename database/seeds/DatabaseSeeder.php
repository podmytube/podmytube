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
        if (!App::environment('production')) {
            Model::unguard();

            $this->call([
                UsersTableSeeder::class,
                PlansTableSeeder::class,
                ChannelsTableSeeder::class,
                CategoriesTableSeeder::class,
                StripePlansTableSeeder::class,
                ApiKeysTableSeeder::class,
            ]);

            Model::reguard();
        }
    }
}
