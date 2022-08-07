<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StripePlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'is_yearly' => true,
            'stripe_live_id' => 'price_live_' . Str::random(4),
            'stripe_test_id' => 'price_test_' . Str::random(4),
            'comment' => null,
        ];
    }
}
