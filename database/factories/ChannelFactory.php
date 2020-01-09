<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

$factory->define(App\Channel::class, function ($faker, array $attributes = []) {

    return [
        'channel_id' => $faker->regexify('[a-zA-Z0-9-_]{24}'),
        'channel_name' => $faker->sentence($nbWords = "3", $variableNbWords = true),
        'authors' => $faker->name,
        'email' => $faker->safeEmail,
        'description' => $faker->text(300),
        'link' => 'http://' . $faker->domainName(),
        'lang' => $faker->randomElement(['FR', 'EN']),
         'user_id' => function () use ($attributes) {
            return factory(App\User::class)->create($attributes)->user_id;
        },
        'category_id' => function () {
            return App\Category::all()->random()->id;
        },
    ];
});
