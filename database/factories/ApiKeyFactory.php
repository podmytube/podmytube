<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApiKeyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'apikey' => 'fake_apikey_mxyzptlk',
            'comment' => 'used for test',
            'active' => true,
        ];
    }
}
