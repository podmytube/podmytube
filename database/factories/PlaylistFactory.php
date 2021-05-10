<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Channel;
use App\Playlist;
use Faker\Generator as Faker;

$factory->define(Playlist::class, function (Faker $faker, $attributes) {
    return [
        'channel_id' => $attributes['channel_id'] ?? function () {
            return factory(Channel::class)->create()->channel_id;
        },
        'youtube_playlist_id' => $attributes['youtube_playlist_id'] ?? 'FAKE-' . $faker->regexify('[a-zA-Z0-9]{10}'),
        'title' => $faker->words('3', true),
        'description' => <<<EOD
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vestibulum fermentum ex luctus posuere. 
        Pellentesque eget tristique orci. Proin convallis malesuada mauris. 
        Donec ultricies magna odio, eget vulputate ligula molestie vitae. 
        Duis quis velit dictum mauris lobortis porta et sollicitudin ante.
        EOD,
        'active' => $attributes['active'] ?? false,
    ];
});
