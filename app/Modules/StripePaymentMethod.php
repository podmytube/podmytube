<?php

declare(strict_types=1);

namespace App\Modules;

use Stripe\PaymentMethod;
use Stripe\StripeClient;

class StripePaymentMethod
{
    protected PaymentMethod $paymentMethod;

    private function __construct(protected StripeClient $stripeClient)
    {
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function createYesCard(): self
    {
        $this->paymentMethod = $this->stripeClient->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 11,
                'exp_year' => 2022,
                'cvc' => '314',
            ],
        ]);

        return $this;
    }

    public function get(): PaymentMethod
    {
        return $this->paymentMethod;
    }
}
