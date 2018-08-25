<?php

use Illuminate\Database\Seeder;

use App\MediasStats;

use App\Medias;

use Carbon\Carbon;

class mediasStatsTableSeeder extends Seeder
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

        // emptying table
        MediasStats::truncate();

        $channels_to_seed = [
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
            
            // creating N random rows to have more than 1 media_id downloaded by day (or 0)
                for ($i = 1; $i < rand(1, 2); $i++) {
                
                // getting one random media
                    $media = Medias::select('media_id')->where('channel_id', 'UC0E0g7aXin1YGKO0QTwhkZw')->inRandomOrder()->first();

                    $results[] = [
                        'channel_id' => $channel_id, // Lola Lol hard coded
                        'media_id' => $media->media_id,
                        'media_day' => $cur_date->toDateString(),
                        'media_cpt' => $faker->numberBetween($min = 0, $max = 9999) // someday 0 some other days ... wooooooo        
                    ];


                }
            }
        }
        // inserting array
        MediasStats::insert($results);
    }
}
