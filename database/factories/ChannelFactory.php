<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Channel::class, function (
    Faker $faker,
    array $attributes = []
) {
    $channel_id = $faker->regexify('[a-zA-Z0-9-_]{24}');
    /** for one channel created I have to create one subscription too */
    factory(App\Subscription::class)->create(['channel_id' => $channel_id,]);

    return [
        'channel_id' => $channel_id,
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
        'link' => 'http://' . $faker->domainName(),
        'category_id' => function () {
            return App\Category::all()->random()->id;
        },
        'lang' => $attributes['lang'] ?? $faker->randomElement(['FR', 'EN']),
        'explicit' => $faker->boolean(),
        'channel_createdAt' => $attributes['created_at'] ?? $faker->dateTimeInInterval('now', '-5 days'),
        'channel_updatedAt' => $faker->dateTimeInInterval('now', '-3 days'),
        'podcast_updatedAt' => $faker->dateTimeInInterval('now', '-2 days'),
        'accept_video_by_tag' => isset($attributes['accept_video_by_tag'])
            ? $attributes['accept_video_by_tag']
            : ($faker->boolean() === true
                ? 'podcast'
                : null),
        'reject_video_by_keyword' => isset($attributes['reject_video_by_keyword'])
            ? $attributes['reject_video_by_keyword']
            : ($faker->boolean() === true
                ? 'donotconvert'
                : null),
        'reject_video_too_old' => isset($attributes['reject_video_too_old'])
            ? $attributes['reject_video_too_old']
            : $faker->dateTimeThisYear()->format('d/m/Y'),
    ];
});
