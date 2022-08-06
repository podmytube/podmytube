<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Channel;
use App\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'channel_id' => Channel::factory(),
            'plan_id' => Plan::factory(),
        ];
    }
}
