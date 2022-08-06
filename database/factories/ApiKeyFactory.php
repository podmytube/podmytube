<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApiKeyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'apikey' => fake()->regexify('[a-zA-Z0-9-_]{24}'),
            'comment' => 'used for test',
            'active' => true,
        ];
    }
}
