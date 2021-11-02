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

    public function create(User $user)
    {
        $this->customer = Customer::create([
            'email' => $user->email,
            'payment_method' => 'pm_1FWS6ZClCIKljWvsVCvkdyWg',
            'invoice_settings' => [
              'default_payment_method' => 'pm_1FWS6ZClCIKljWvsVCvkdyWg',
            ],
        ]);
    }
}
