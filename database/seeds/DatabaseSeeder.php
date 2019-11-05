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
        if (!App::environment('prod')) {
            
            Model::unguard();

            $this->call([
                UsersTableSeeder::class,
                ChannelTableSeeder::class,
                CategoriesTableSeeder::class,
                PlansTableSeeder::class,
                SubscriptionTableSeeder::class,
                StripePlansTableSeeder::class,
            ]);

            Model::reguard();
        }

	}
}
