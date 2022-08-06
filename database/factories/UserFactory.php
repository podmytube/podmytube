<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'firstname' => fake()->firstname(),
            'lastname' => fake()->lastname(),
            'email' => fake()->email(),
            'password' => '$2y$10$rIo.zLS88CNtH66fSa4DOOYkzPIq8RGkS.DqyG/AoYOUI272HD5Sa', // secret
            'remember_token' => Str::random(10),
            'newsletter' => true,
            'superadmin' => false,
            'stripe_id' => null,
            'dont_warn_exceeding_quota' => false,
        ];
    }
}
