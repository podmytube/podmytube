<?php

use App\ApiKey;
use App\Quota;
use Carbon\Carbon;
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

$factory->define(Quota::class, function (Faker $faker, array $attributes = []) {
    return [
        'apikey_id' => $attributes['apikey_id'] ??
            function () {
                return factory(ApiKey::class)->create()->id;
            },
        'script' => $attributes['script'] ?? $faker->regexify('[a-z]{8}') . '.php',
        'quota_used' => $attributes['quota_used'] ?? $faker->numberBetween(100, 200),
        'created_at' => Carbon::now(),
    ];
});
