<?php

declare(strict_types=1);

namespace App\Modules;

use Stripe\StripeClient;
use Stripe\Subscription;

class StripeSubscription
{
    protected Subscription $subscription;

    private function __construct(protected StripeClient $stripeClient)
    {
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function retrieve(string $subscriptionId): self
    {
        $this->subscription = $this->stripeClient->subscriptions->retrieve($subscriptionId);
        return $this;
    }

    public function subscriptionId(): string
    {
        return $this->subscription->id;
    }

    public function customerId(): string
    {
        return $this->subscription->customer;
    }

    public function status(): string
    {
        return $this->subscription->status;
    }

    public function plan()
    {
        dd($this->subscription->metadata);
    }
}
