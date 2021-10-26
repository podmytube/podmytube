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
        $revenues = 0;
        // get all active subscriptions
        Subscription::query()
            ->with('plan')
            ->whereNull('ends_at')
            ->orWhere('ends_at', '>', now())
            ->get()
            ->each(function (Subscription $subscription) use (&$revenues): void {
                // on each subscription get plan price
                $revenues += $subscription->plan->price;
            })
        ;

        return $revenues;
    }
}
