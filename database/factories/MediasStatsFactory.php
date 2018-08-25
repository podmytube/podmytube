<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\medias_stats::class, function (Faker $faker) {
    static $channels = [
        'UC0E0g7aXin1YGKO0QTwhkZw', // Lola Lol
    ];

    return [
        'channel_id' => $channels[rand(0,count($channels)-1)], // getting one real channel is more simple
        'media_id' => $faker->regexify('[a-zA-Z0-9-_]{6}'), // foo media id 
        'media_day' => $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null), // over a long period
        'media_cpt' => $faker->numberBetween($min=0,$max=9999) // someday 0 some other days ... wooooooo        
    ];
});
