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
        if (App::environment('dev')) {
            // The environment is dev
            $this->call([
                usersTableSeeder::class,
                categoriesTableSeeder::class,
                mediasStatsTableSeeder::class,
                appStatsTableSeeder::class,
                thumbsTableSeeder::class,
            ]);
        } else {
            $this->call([
                thumbsTableSeeder::class,
            ]);
        }

	}
}
