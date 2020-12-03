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

$factory->define(App\User::class, function (Faker $faker, array $attributes = []) {
    return [
        'firstname' => $faker->firstname,
        'lastname' => $faker->lastname,
        'email' => $attributes['email'] ?? $faker->safeEmail,
        'password' => $attributes['password'] ?? bcrypt('secret'),
        'remember_token' => Str::random(10),
    ];
});
