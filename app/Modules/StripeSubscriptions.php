<?php

declare(strict_types=1);

namespace App\Modules;

use Stripe\StripeClient;
use Stripe\Collection as StripeCollection;

class StripeSubscriptions
{
    private function __construct(protected StripeClient $stripeClient)
    {
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function subscriptions(): StripeCollection
    {
        return $this->stripeClient->subscriptions->all();
    }
}
