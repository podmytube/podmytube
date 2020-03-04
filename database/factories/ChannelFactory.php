<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Channel::class, function (Faker $faker, array $attributes = []) {
    return [
        'channel_id' => $faker->regexify('[a-zA-Z0-9-_]{24}'),
        'user_id' => $attributes['user_id'] ?? function () use ($attributes) {
            return factory(App\User::class)->create($attributes)->user_id;
        },
        'channel_name' => $faker->sentence($nbWords = "3", $variableNbWords = true),
        'podcast_title' => $faker->sentence($nbWords = "5", $variableNbWords = true),
        'podcast_copyright' => $faker->sentence($nbWords = "10", $variableNbWords = true),
        'authors' => $faker->name,
        'email' => $faker->safeEmail,
        'description' => $faker->text(300),
        'link' => 'http://' . $faker->domainName(),
        'category_id' => function () {
            return App\Category::all()->random()->id;
        },
        'lang' => $faker->randomElement(['FR', 'EN']),
        'explicit' => $faker->boolean(),
    ];
});
