<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Subscription;
use Faker\Generator as Faker;

$factory->define(Subscription::class, function (
    Faker $faker,
    $attributes = []
) {
    return [
        'channel_id' => $attributes['channel_id'] ?? $faker->regexify('[a-zA-Z0-9-_]{24}'),
        'plan_id' => $attributes['plan_id'] ?? $faker->numberBetween(1, 7),
    ];
});
