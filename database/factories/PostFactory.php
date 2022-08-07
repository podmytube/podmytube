<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PostCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->title;

        return [
            'wp_id' => fake()->randomNumber(2),
            'author' => fake()->firstname,
            'title' => $title,
            'slug' => Str::slug($title),
            'featured_image' => fake()->imageUrl(),
            'sticky' => fake()->boolean(5), // 5% chances to be true
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'format' => 'standard',
            'status' => true, // api does not export unpublished/draft posts - I keek this to eventually disable one post from pod.
            'published_at' => fake()->dateTimeBetween('1 month ago', 'now'),
            'post_category_id' => PostCategory::factory(),
        ];
    }
}
