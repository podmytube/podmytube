<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use Faker\Generator as Faker;

$factory->define(Category::class, function (
    Faker $faker,
    $attributes = []
) {
    return [
        'parent_id' => 0,
        'name' => $faker->word,
    ];
});
