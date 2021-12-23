<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use App\Exceptions\ChannelOwnerMismatchingStripeException;
use App\Exceptions\EmptyChannelIdReceivedFromStripeException;
use App\Exceptions\EmptyCustomerReceivedFromStripeException;
use App\Exceptions\EmptySubscriptionReceivedFromStripeException;
use App\Exceptions\InvalidSubscriptionReceivedFromStripeException;
use App\Exceptions\UnknownChannelIdReceivedFromStripeException;
use App\Jobs\StripeWebhooks\HandleCheckoutSessionCompleted;
use App\Plan;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Stripe\StripeClient;
use Stripe\Subscription;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class StripeWebhooksTest extends TestCase
{
    use RefreshDatabase;

    protected const STRIPE_ROUTE = '/stripe/webhooks';
    protected const HTTP_METHOD_NOT_ALLOWED = 405;
    protected const TEST_STRIPE_CUSTOMER_ID = 'cus_testid';
    protected const TEST_STRIPE_SUBSCRIPTION_ID = 'sub_testid';

    protected MockInterface $mockedStripeClient;
    protected MockInterface $mockedStripeCustomer;
    protected MockInterface $mockedStripeSubscription;

    public function setUp(): void
    {
        $this->markTestSkipped("TO BE MOCKED");
        parent::setUp();
        // setting signature check to false. I only need to check my part
        config(['stripe-webhooks.verify_signature' => false]);

        // $this->stripeClient = new StripeClient(config('app.stripe_secret'));
        $this->seedStripePlans();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function get_should_fail(): void
    {
        $this->get(self::STRIPE_ROUTE)
            ->assertStatus(self::HTTP_METHOD_NOT_ALLOWED)
        ;
    }

    /** @test */
    public function incomplete_post_no_user_should_fail(): void
    {
        $this->postJson(self::STRIPE_ROUTE, ['type' => 'checkout.session.completed'])
            ->assertStatus(500)
            ->assertJson([
                'exception' => EmptyCustomerReceivedFromStripeException::class,
            ])
        ;
    }

    /** @test */
    public function incomplete_post_no_channel_should_fail(): void
    {
        $user = factory(User::class)->create();
        $this->postJson(
            self::STRIPE_ROUTE,
            [
                'type' => 'checkout.session.completed',
                'data' => ['object' => ['customer_email' => $user->email]],
            ]
        )
            ->assertStatus(500)
            ->assertJson([
                'exception' => EmptyChannelIdReceivedFromStripeException::class,
            ])
        ;
    }

    /** @test */
    public function incomplete_post_invalid_channel_should_fail(): void
    {
        $user = factory(User::class)->create();
        $this->postJson(
            self::STRIPE_ROUTE,
            [
                'type' => 'checkout.session.completed',
                'data' => [
                    'object' => [
                        'customer_email' => $user->email,
                        'metadata' => [
                            'channel_id' => 'invalid-channel-id',
                        ],
                    ],
                ],
            ]
        )
            ->assertStatus(500)
            ->assertJson([
                'exception' => UnknownChannelIdReceivedFromStripeException::class,
            ])
        ;
    }

    /** @test */
    public function incomplete_post_channel_not_owned_by_user_should_fail(): void
    {
        $user = factory(User::class)->create();
        $channel = factory(Channel::class)->create();
        $this->postJson(
            self::STRIPE_ROUTE,
            [
                'type' => 'checkout.session.completed',
                'data' => [
                    'object' => [
                        'customer_email' => $user->email,
                        'metadata' => [
                            'channel_id' => $channel->id(),
                        ],
                    ],
                ],
            ]
        )
            ->assertStatus(500)
            ->assertJson([
                'exception' => ChannelOwnerMismatchingStripeException::class,
            ])
        ;
    }

    /**
     * @test
     * In checkout.session.completed I only get the subscription Id.
     * I will use it to ask Stripe::api what plan is subscribed in subscription.
     * No subscription id => mean problem
     */
    public function incomplete_post_no_subscription_should_fail(): void
    {
        $channel = factory(Channel::class)->create();
        $this->postJson(
            self::STRIPE_ROUTE,
            [
                'type' => 'checkout.session.completed',
                'data' => [
                    'object' => [
                        'customer_email' => $channel->user->email,
                        'metadata' => [
                            'channel_id' => $channel->id(),
                        ],
                    ],
                ],
            ]
        )
            ->assertStatus(500)
            ->assertJson([
                'exception' => EmptySubscriptionReceivedFromStripeException::class,
            ])
        ;
    }

    /** @test */
    public function incomplete_post_invalid_subscription_should_fail(): void
    {
        $channel = factory(Channel::class)->create();
        $this->postJson(
            self::STRIPE_ROUTE,
            [
                'type' => 'checkout.session.completed',
                'data' => [
                    'object' => [
                        'customer_email' => $channel->user->email,
                        'subscription' => 'invalid-subscription',
                        'metadata' => [
                            'channel_id' => $channel->id(),
                        ],
                    ],
                ],
            ]
        )
            ->assertStatus(500)
            ->assertJson([
                'exception' => InvalidSubscriptionReceivedFromStripeException::class,
            ])
        ;
    }

    /** @test */
    public function complete_post_should_succeed(): void
    {
        // creating user that will subscribe
        $user = factory(User::class)->create(['stripe_id' => self::TEST_STRIPE_CUSTOMER_ID]);
        $channel = factory(Channel::class)->create(['user_id' => $user->user_id]);

        $this->postJson(
            self::STRIPE_ROUTE,
            [
                'type' => 'checkout.session.completed',
                'data' => [
                    'object' => [
                        'customer_email' => $channel->user->email,
                        'subscription' => self::TEST_STRIPE_SUBSCRIPTION_ID,
                        'metadata' => [
                            'channel_id' => $channel->id(),
                        ],
                    ],
                ],
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                'message' => HandleCheckoutSessionCompleted::ERROR_MESSAGE_NO_ERROR,
            ])
        ;

        // cleaning
    }
}
