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
                CategoriesTableSeeder::class,
                ChannelTableSeeder::class,
                PlansTableSeeder::class,
                SubscriptionTableSeeder::class,
                StripePlansTableSeeder::class,
                ApiKeysTableSeeder::class,
            ]);

            Model::reguard();
        }

	}
}
