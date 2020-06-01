<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Channel;
use Faker\Generator as Faker;

$factory->define(App\Media::class, function (Faker $faker, $attributes) {
    /** preparing published and grabbed at period */
    $publishedAt =
        $attributes['publishedAt'] ??
        $faker->dateTimeBetween('-30 days', 'yesterday');
    $grabbedAt =
        $attributes['grabbedAt'] ??
        $faker->dateTimeBetween($publishedAt, 'now');

    /** returning our nice new media */
    return [
        'media_id' => $faker->regexify('[a-zA-Z0-9-_]{8}'),
        'channel_id' =>
            $attributes['channel_id'] ??
            function () use ($attributes) {
                return factory(Channel::class)->create()->channel_id;
            },
        'title' => $faker->sentence($nbWords = '3', $variableNbWords = true),
        'description' => $faker->text(300),
        'length' => $faker->numberBetween(1000, 40000),
        'duration' => $faker->numberBetween(30, 65535),
        'published_at' => $publishedAt,
        'grabbed_at' => $grabbedAt,
        'active' => true,
    ];
});
