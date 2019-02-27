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
        if (App::environment(['dev','local','rec'])) {
            // The environment is dev
            $this->call([
                usersTableSeeder::class,
                categoriesTableSeeder::class,
            ]);
        }

	}
}
