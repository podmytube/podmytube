<?php

use App\Plan;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Plan::class, function (Faker $faker, array $attributes = []) {
    $name = $faker->word();
    return [
        'name' => $name,
        'slug' => Str::slug($name),
        'price' => 29,
        'billing_yearly' => false,
        'nb_episodes_per_month' => 5,
    ];
});
