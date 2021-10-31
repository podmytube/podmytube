<?php

declare(strict_types=1);

namespace App\Factories;

use App\Subscription;

class RevenueFactory
{
    private function __construct()
    {
        //code
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function get(): int
    {
        // get all active subscriptions
        return Subscription::query()
            ->with('plan')
            ->whereNull('ends_at')
            ->orWhere('ends_at', '>', now())
            ->get()
            ->reduce(function ($carry, Subscription $subscription): int {
                // on each subscription get plan price
                return $carry + $subscription->plan->price;
            }, 0)
        ;
    }
}
