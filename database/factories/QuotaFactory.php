<?php

use App\Quota;
use Carbon\Carbon;
use Illuminate\Support\Str;
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
        'apikey_id' =>
            $attributes['apikey_id'] ??
            function () {
                return factory(App\ApiKey::class)->create()->id;
            },
        'script' => $faker->regexify('[a-z]{8}') . '.php',
        'quota_used' => $faker->numberBetween(2000, 20000),
        'created_at' => $attributes['created_at'] ?? Carbon::now(),
    ];
});
