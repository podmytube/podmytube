<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Modules\StripeSubscriptions;
use Stripe\Collection as StripeCollection;
use Stripe\StripeClient;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class StripeSubscriptionsTest extends TestCase
{
    protected StripeClient $stripeClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->stripeClient = new StripeClient(env('STRIPE_SECRET'));
    }

    /** @test */
    public function subscriptions_is_fine(): void
    {
        $result = StripeSubscriptions::init($this->stripeClient)->subscriptions();
        $this->assertNotNull($result);
        $this->assertInstanceOf(StripeCollection::class, $result);
    }
}
