<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApiKeyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'apikey' => 'apikey_' . fake()->bothify('???###'),
            'comment' => 'used for test',
            'active' => true,
        ];
    }
}
