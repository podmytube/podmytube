<?php

use Illuminate\Database\Seeder;

use App\User;

class usersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
         // emptying table AppStats
        User::truncate();

		// creating my own user
		User::insert([
			'user_id'                => 1,
			'name'                   => 'Fred',
			'email'                  => 'frederick@podmytube.com',
			'password'               => '$2y$10$c5.Md.ZOCYFv70pPv/3nAeNSsXzV3ttFguIu.GzEFYhaijHh988se',
		]);

		User::insert([
			'user_id'                => 2,
			'name'                   => 'Julia',
			'email'                  => 'juliactx@gmail.com',
			'password'               => '$2y$10$qhdmxqbOtTOHyGqw8AoLSuigHDsg9gdxULMMOefUXfigSTrG6tfO6',
		]); 

		/**
		 * giving the Lola Lol Channel to me :)
		 */
		DB::table('channels')
			->where('channel_id','UC9hHeywcPBnLglqnQRaNShQ')
			->update(['user_id' => 1 ]);

		/**
		 * giving the Lola Lol Channel to me :)
		 */
		DB::table('channels')
			->where('channel_id','UCBXJGoueIDn_uHpvMWv_cRQ')
			->update(['user_id' => 2 ]);

	}
}
