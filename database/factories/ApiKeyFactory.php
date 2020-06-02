<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ApiKey;
use Faker\Generator as Faker;

$factory->define(ApiKey::class, function (Faker $faker, $attributes = []) {
    return [
        'apikey' =>
            $attributes['apikey'] ?? $faker->regexify('[a-zA-Z0-9-_]{24}'),
        'comment' => 'used for test',
        'active' => true,
    ];
});
