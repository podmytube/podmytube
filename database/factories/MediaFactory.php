<?php

declare(strict_types=1);

// @var $factory \Illuminate\Database\Eloquent\Factory

use App\Channel;
use App\Media;
use Faker\Generator as Faker;

$factory->define(Media::class, function (Faker $faker, $attributes) {
    $grabbedAt = null;
    $length = 0;
    $duration = 0;
    if (isset($attributes['grabbed_at'])) {
        $length = 355;
        $duration = 2500;
        $grabbedAt = $attributes['grabbed_at'];
    }

    // returning our nice new media
    return [
        'media_id' => $attributes['media_id'] ?? $faker->regexify('[a-zA-Z0-9-_]{4}'),
        'channel_id' => $attributes['channel_id'] ?? function () {
            return factory(Channel::class)->create()->channel_id;
        },
        'title' => $attributes['title'] ?? $faker->words(2, true),
        'description' => $attributes['description'] ?? <<<'EOT'
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor. 
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi utt.
EOT,
        'length' => $length,
        'duration' => $duration,
        'published_at' => $attributes['published_at'] ?? now(),
        'grabbed_at' => $grabbedAt,
        'status' => $attributes['status'] ?? Media::STATUS_NOT_DOWNLOADED,
    ];
});
