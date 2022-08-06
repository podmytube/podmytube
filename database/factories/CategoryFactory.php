<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->word();

        return [
            'parent_id' => $attributes['parent_id'] ?? 0,
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
