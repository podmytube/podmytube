<?php

declare(strict_types=1);

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Language;
use Faker\Generator as Faker;

$factory->define(Language::class, function (
    Faker $faker,
    $attributes = []
) {
    $name = $attributes['iso_name'] ?? $faker->word();

    return [
        'code' => strtolower(substr($name, 0, 2)),
        'iso_name' => $name,
        'native_name' => $name,
    ];
});
