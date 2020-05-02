<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Channel;
use App\Subscription;
use Faker\Generator as Faker;

$factory->define(Subscription::class, function (
    Faker $faker,
    $attributes = []
) {
    $plan_id = null;
    if (isset($attributes['plan_id'])) {
        $plan_id = $attributes['plan_id'];
    }

    unset($attributes['plan_id']);

    return [
        'channel_id' =>
            $attributes['channel_id'] ??
            function () use ($attributes) {
                return factory(Channel::class)->create($attributes)->channel_id;
            },
        'plan_id' => $plan_id ?? $faker->numberBetween(1, 7),
    ];
});
