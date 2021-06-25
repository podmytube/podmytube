<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use App\Channel;
use App\Language;
use App\User;
use Faker\Generator as Faker;

$factory->define(Channel::class, function (Faker $faker, $attributes) {
    return [
        'channel_id' => $attributes['channel_id'] ?? 'FAKE-' . $faker->regexify('[a-zA-Z0-9]{6}'),
        'user_id' => $attributes['user_id'] ?? function () {
            return factory(User::class)->create()->user_id;
        },
        'channel_name' => $faker->words('2', true),
        'podcast_title' => $attributes['podcast_title'] ?? $faker->words('3', true),
        'podcast_copyright' => $attributes['podcast_copyright'] ?? $faker->sentence('4', true),
        'authors' => $attributes['authors'] ?? 'John Lorem',
        'email' => $attributes['email'] ?? 'john@loremipsum.com',
        'link' => $attributes['link'] ?? 'https://loremipsum.com',
        'category_id' => $attributes['category_id'] ?? function () {
            return factory(Category::class)->create()->id;
        },
        'language_id' => $attributes['language_id'] ?? function () {
            return factory(Language::class)->create()->id;
        },
        'explicit' => $attributes['explicit'] ?? false,
        'active' => $attributes['explicit'] ?? true,
        'channel_createdAt' => $attributes['created_at'] ?? now()->subDays(5),
        'channel_updatedAt' => now()->subDays(3),
        'podcast_updatedAt' => now()->subDays(2),
        'accept_video_by_tag' => $attributes['accept_video_by_tag'] ?? null,
        'reject_video_by_keyword' => $attributes['reject_video_by_keyword'] ?? null,
        'reject_video_too_old' => $attributes['reject_video_too_old'] ?? null,
        'description' => <<<EOD
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vestibulum fermentum ex luctus posuere. 
Pellentesque eget tristique orci. Proin convallis malesuada mauris. 
Donec ultricies magna odio, eget vulputate ligula molestie vitae. 
Duis quis velit dictum mauris lobortis porta et sollicitudin ante.
EOD,
    ];
});
