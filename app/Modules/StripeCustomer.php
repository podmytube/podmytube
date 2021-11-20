<?php

declare(strict_types=1);

namespace App\Modules;

use App\User;
use Stripe\Customer;
use Stripe\StripeClient;

class StripeCustomer
{
    protected Customer $customer;

    private function __construct(protected StripeClient $stripeClient)
    {
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function create(User $user): self
    {
        $this->customer = $this->stripeClient->customers->create([
            'email' => $user->email,
        ]);

        return $this;
    }

    public function get(): Customer
    {
        return $this->customer;
    }

    public function email(): string
    {
        return $this->customer->email;
    }

    public function customerId(): string
    {
        return $this->customer->id;
    }

    public function delete(): bool
    {
        $result = $this->stripeClient->customers->delete($this->customer->id);

        return $result->deleted === true;
    }
}
