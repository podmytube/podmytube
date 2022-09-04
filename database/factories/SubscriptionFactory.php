<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Plan;
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

    public function plan(Plan $plan): static
    {
        return $this->state(['plan_id' => $plan->id]);
    }
}
