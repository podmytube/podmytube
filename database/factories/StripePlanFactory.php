<?php

use App\Plan;
use App\StripePlan;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(StripePlan::class, function (Faker $faker, array $attributes = []) {
    return [
        'plan_id' => $attributes['plan_id'] ?? function () {
            return factory(Plan::class)->create()->id;
        },
        'is_yearly' => $attributes['is_yearly'] ?? true,
        'stripe_live_id' => $attributes['stripe_live_id'] ?? 'price_live_' . Str::random(4),
        'stripe_test_id' => $attributes['stripe_test_id'] ?? 'price_test_' . Str::random(4),
        'comment' => null
    ];
});
