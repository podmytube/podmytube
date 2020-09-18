<?php

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

$factory->define(App\PostCategory::class, function (Faker $faker, array $attributes = []) {
    $name = $faker->words(2, true);
    return [
        'wp_id' => $attributes['wp_id'] ?? $faker->randomNumber(2),
        'name' => $attributes['name'] ?? $name,
        'slug' => Str::slug($name),
    ];
});
