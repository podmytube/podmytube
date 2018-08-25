<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

use App\UserAgents;

use App\AppStats;


class appStatsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // faker init 
        $faker = \Faker\Factory::create();

        // emptying table AppStats
        AppStats::truncate();

		$channels_to_seed=[
			'UC9hHeywcPBnLglqnQRaNShQ',
			'UCBXJGoueIDn_uHpvMWv_cRQ'
		];

                // array we are going to insert
        $results = [];
		foreach ($channels_to_seed as $channel_id) {
			// creating factory fakes results from starting_date to now
			$starting_date = new Carbon('1 month ago');
			$ending_date = Carbon::now();


			// loop between starting_date & ending_date 
			for ($cur_date = $starting_date; $cur_date <= $ending_date; $cur_date->addDay()) {

				// creating N random rows to have more than 1 User Agents by day (or 0)
				for ($i = 1; $i < rand(1, 5); $i++) {
					// getting one random user agent
					$ua = UserAgents::select('id')->inRandomOrder()->first(); 

					$results[] = [
						'channel_id' => $channel_id,
						'app_day' => $cur_date->toDateString(), 
						'ua_id' => $ua->id,
						'app_cpt' => $faker->numberBetween($min = 0, $max = 9999) // someday 0 some other days ... wooooooo        
					];

				}
			}
		}
        // inserting array
        AppStats::insert($results);
    }
}
