<?php

declare(strict_types=1);

namespace Database\Factories;

use App\ApiKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'apikey_id' => ApiKey::factory(),
            'script' => fake()->regexify('[a-z]{8}') . '.php',
            'quota_used' => fake()->numberBetween(100, 200),
            'created_at' => now(),
        ];
    }
}
