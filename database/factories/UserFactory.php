<?php

declare(strict_types=1);

use Faker\Generator as Faker;
use Illuminate\Support\Str;

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
        'firstname' => $attributes['firstname'] ?? $faker->firstname(),
        'lastname' => $attributes['lastname'] ?? $faker->lastname(),
        'email' => $attributes['email'] ?? $faker->email(),
        'password' => $attributes['password'] ?? '$2y$10$rIo.zLS88CNtH66fSa4DOOYkzPIq8RGkS.DqyG/AoYOUI272HD5Sa', //secret
        'remember_token' => Str::random(10),
        'newsletter' => $attributes['newsletter'] ?? true,
    ];
});
