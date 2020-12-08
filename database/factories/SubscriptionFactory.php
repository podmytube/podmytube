<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Channel;
use App\Plan;
use App\Subscription;
use Faker\Generator as Faker;

$factory->define(Subscription::class, function (
    Faker $faker,
    $attributes = []
) {
    return [
        'channel_id' => $attributes['channel_id'] ?? function () {
            return factory(Channel::class)->create()->channel_id;
        },
        'plan_id' => $attributes['plan_id'] ?? function () {
            return factory(Plan::class)->create()->id;
        },
    ];
});
