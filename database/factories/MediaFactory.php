<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Channel;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Media::class, function (Faker $faker, $attributes) {
    /** preparing published and grabbed at period */
    $publishedAt =
        $attributes['published_at'] ??
        $faker->dateTimeBetween(Carbon::now()->startOfMonth(), Carbon::now());

    $grabbedAt =
        $attributes['grabbed_at'] ??
        $faker->dateTimeBetween($publishedAt, Carbon::now());

    /** returning our nice new media */
    return [
        'media_id' => $faker->regexify('[a-zA-Z0-9-_]{8}'),
        'channel_id' =>
        $attributes['channel_id'] ??
            function () {
                return factory(Channel::class)->create()->channel_id;
            },
        'title' => 'Lorem ipsum dolor sit amet',
        'description' => <<<EOT
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
EOT,
        'length' => $faker->numberBetween(100, 400),
        'duration' => $faker->numberBetween(300, 600),
        'published_at' => $publishedAt,
        'grabbed_at' => $grabbedAt,
        'active' => true,
    ];
});
