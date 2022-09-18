<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlanFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'price' => 9,
            'billing_yearly' => false,
            'nb_episodes_per_month' => 5,
        ];
    }

    public function isFree(): static
    {
        $name = 'forever free';

        return $this->state([
            'name' => $name,
            'slug' => Str::slug($name),
            'price' => 0,
            'nb_episodes_per_month' => 1,
        ]);
    }

    public function name(string $name): static
    {
        return $this->state([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
    }
}
