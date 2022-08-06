<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'wp_id' => fake()->randomNumber(2),
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
