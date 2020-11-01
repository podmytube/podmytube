<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use App\Channel;
use Faker\Generator as Faker;

$factory->define(Channel::class, function (Faker $faker, $attributes) {
    return [
        'channel_id' => $faker->regexify('[a-zA-Z0-9-_]{24}'),
        'user_id' => $attributes['user_id'] ??
            function () {
                return factory(App\User::class)->create()->user_id;
            },
        'channel_name' => $faker->sentence('3', true),
        'podcast_title' => $faker->sentence('5', true),
        'podcast_copyright' => $faker->sentence('10', true),
        'authors' => $faker->name,
        'email' => $faker->safeEmail,
        'description' => $faker->text(300),
        'link' => 'https://loremipsum.com',
        'category_id' => $attributes['category_id'] ?? function () {
            return factory(Category::class)->create()->id;
        },
        'lang' => $attributes['lang'] ?? $faker->randomElement(['FR', 'EN']),
        'explicit' => $faker->boolean(),
        'channel_createdAt' => $attributes['created_at'] ?? $faker->dateTimeInInterval('now', '-5 days'),
        'channel_updatedAt' => $faker->dateTimeInInterval('now', '-3 days'),
        'podcast_updatedAt' => $faker->dateTimeInInterval('now', '-2 days'),
        'accept_video_by_tag' => $attributes['accept_video_by_tag'] ?? null,
        'reject_video_by_keyword' => $attributes['reject_video_by_keyword'] ?? null,
        'reject_video_too_old' => $attributes['reject_video_too_old'] ?? null,
    ];
});
