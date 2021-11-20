<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Modules\StripeCustomer;
use App\Modules\StripeSubscription;
use App\StripePlan;
use App\User;
use Stripe\StripeClient;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class StripeSubscriptionTest extends TestCase
{
    protected StripeClient $stripeClient;
    protected ?StripeSubscription $stripeSubscription = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedStripePlans($withPlans = true);
        $this->stripeClient = new StripeClient(config('app.stripe_secret'));
    }

    public function tearDown(): void
    {
        if ($this->stripeSubscription !== null) {
            $this->stripeSubscription->cancel();
        }
        parent::tearDown();
    }

    /** @test */
    public function subscription_creation_is_fine(): void
    {
        // creating one user
        $user = factory(User::class)->create();
        $this->stripeCustomer = StripeCustomer::init($this->stripeClient)->create($user);
        $user->update(['stripe_id' => $this->stripeCustomer->customerId()]);

        // getting one plan
        $plan = $this->getPlanBySlug('starter');
        $stripePlanId = StripePlan::priceIdForPlanAndBilling($plan, false, false);

        $this->stripeSubscription = StripeSubscription::init($this->stripeClient)->create($user, $stripePlanId);
    }
}
