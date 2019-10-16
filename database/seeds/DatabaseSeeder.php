<?php

use Illuminate\Database\Seeder;

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
                usersTableSeeder::class,
                categoriesTableSeeder::class,
                plansTableSeeder::class,
                subscriptionTableSeeder::class,
                stripePlansTableSeeder::class,
            ]);

            Model::reguard();
        }

	}
}
