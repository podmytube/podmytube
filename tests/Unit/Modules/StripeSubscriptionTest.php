<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Modules\StripeSubscription;
use Stripe\StripeClient;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class StripeSubscriptionTest extends TestCase
{
    protected StripeClient $stripeClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->stripeClient = new StripeClient(env('STRIPE_SECRET'));
    }

    /** @test */
    public function single_subscription_is_fine_too(): void
    {
        $expectedSubscriptionId = 'sub_JG9l8yNQe3TGe5';
        $expectedCustomerId = 'cus_JG9lduW7rPvVKW';
        $expectedStatus = 'active';

        /**
         * getting one random subscription from the stripe api.
         */
        $subscription = StripeSubscription::init($this->stripeClient)->retrieve($expectedSubscriptionId);
        $this->assertNotNull($subscription);
        $this->assertInstanceOf(StripeSubscription::class, $subscription);
        $this->assertEquals($expectedSubscriptionId, $subscription->subscriptionId());
        $this->assertEquals($expectedCustomerId, $subscription->customerId());
        $this->assertEquals($expectedStatus, $subscription->status());
    }
}
