<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Channel;
use App\User;
use Faker\Generator as Faker;

$factory->define(Channel::class, function (Faker $faker) {
    /**
     * creating fake channel_id
     */
    $channelId = $faker->regexify('[a-zA-Z0-9-_]{24}');

    /**
     * creating fake user
     */
    factory(User::class)->create();

    /**
     * getting one of the random
     */
    $user=User::all()->random();

    /**
     * return the object to be make/created
     */
    return [
        'channel_id' => $channelId,
        'user_id' => $user->user_id,
    ];
});
