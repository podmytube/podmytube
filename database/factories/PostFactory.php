<?php

use App\Post;
use App\PostCategory;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Post::class, function (Faker $faker, array $attributes = []) {
    $title = $faker->title;
    return [
        'wp_id' => $attributes['wp_id'] ?? $faker->randomNumber(2),
        'author' => $attributes['author'] ?? $faker->firstname,
        'title' => $attributes['title'] ?? $title,
        'slug' => Str::slug($title),
        'featured_image' => $attributes['featured_image'] ?? $faker->imageUrl(),
        'sticky' => $attributes['sticky'] ?? $faker->boolean(5), // 5% chances to be true
        'excerpt' => $attributes['excerpt'] ?? $faker->paragraph(),
        'content' => $attributes['content'] ?? $faker->paragraphs(3, true),
        'format' => $attributes['format'] ?? 'standard',
        'status' => true, // api does not export unpublished/draft posts - I keek this to eventually disable one post from pod.
        'published_at' => $attributes['published_at'] ?? $faker->dateTimeBetween('1 month ago', 'now'),
        'category_id' => PostCategory::NEWS,
    ];
});
