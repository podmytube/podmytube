<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Category::class, function (
    Faker $faker,
    $attributes = []
) {
    $name = $attributes['name'] ?? $faker->word();
    return [
        'parent_id' => $attributes['parent_id'] ?? 0,
        'name' => $name,
        'slug' => Str::slug($name)
    ];
});
