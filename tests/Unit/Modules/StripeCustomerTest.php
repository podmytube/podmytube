<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Modules\StripeCustomer;
use App\User;
use Stripe\StripeClient;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class StripeCustomerTest extends TestCase
{
    protected StripeClient $stripeClient;
    protected ?StripeCustomer $stripeCustomer = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->stripeClient = new StripeClient(config('app.stripe_secret'));
    }

    public function tearDown(): void
    {
        if ($this->stripeCustomer !== null) {
            $this->stripeCustomer->delete();
        }
        parent::tearDown();
    }

    /** @test */
    public function customer_creation_is_fine(): void
    {
        $user = factory(User::class)->create();
        $this->stripeCustomer = StripeCustomer::init($this->stripeClient)->create($user);

        $this->assertNotNull($this->stripeCustomer);
        $this->assertInstanceOf(StripeCustomer::class, $this->stripeCustomer);
        $this->assertEquals($user->email, $this->stripeCustomer->email());
    }
}
