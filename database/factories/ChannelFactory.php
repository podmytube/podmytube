<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

$factory->define(App\Channel::class, function ($faker) {
    return [
        'channel_id' => $faker->regexify('[a-zA-Z0-9-_]{24}'),
        'channel_name' => $faker->regexify('[a-zA-Z0-9-_]{24}'),
        'authors' => $faker->name,
        'email' => $faker->safeEmail,
        'description' => $faker->text(300),
        'link' => 'http://' . $faker->domainName(),
        'lang' => $faker->randomElement(['FR', 'EN']),
        'user_id' => function () {
            /** creating user on the fly */
            return factory(App\User::class)->create()->user_id;
        },
        'category_id' => function () {
            /** getting one random category */
            return App\Category::all()->random()->id;
        },
    ];
});
