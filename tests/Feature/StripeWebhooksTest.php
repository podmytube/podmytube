<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Exceptions\ChannelOwnerMismatchingStripeException;
use App\Exceptions\EmptyChannelIdReceivedFromStripeException;
use App\Exceptions\EmptyCustomerReceivedFromStripeException;
use App\Exceptions\EmptySubscriptionReceivedFromStripeException;
use App\Exceptions\InvalidSubscriptionReceivedFromStripeException;
use App\Exceptions\UnknownChannelIdReceivedFromStripeException;
use App\Jobs\StripeWebhooks\HandleCheckoutSessionCompletedJob;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Stripe\StripeClient;
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

    public function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('TO BE DONE');

        Log::debug('foo');
        // setting signature check to false. I only need to check my part
        config(['stripe-webhooks.verify_signature' => false]);

        // $this->stripeClient = new StripeClient(config('app.stripe_secret'));
        $this->seedStripePlans();

        // $this->mockStripe();
    }

    public function tearDown(): void
    {
        // Mockery::close();
    }

    /** @test */
    public function get_should_fail(): void
    {
        $this->markTestSkipped('TO BE DONE');

        $this->get(self::STRIPE_ROUTE)
            ->assertStatus(self::HTTP_METHOD_NOT_ALLOWED)
        ;
    }

    /** @test */
    public function incomplete_post_no_user_should_fail(): void
    {
        $this->markTestSkipped('TO BE DONE');

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
        $this->markTestSkipped('TO BE DONE');

        $user = User::factory()->create();
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
        $this->markTestSkipped('TO BE DONE');

        $user = User::factory()->create();
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
        $this->markTestSkipped('TO BE DONE');

        $user = User::factory()->create();
        $channel = Channel::factory()->create();
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
        $this->markTestSkipped('TO BE DONE');

        $channel = Channel::factory()->create();
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
        $this->markTestSkipped('TO BE DONE');

        $channel = Channel::factory()->create();
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
        $this->markTestSkipped('TO BE DONE');
        // creating user that will subscribe
        $user = User::factory()->create(['stripe_id' => self::TEST_STRIPE_CUSTOMER_ID]);
        $channel = Channel::factory()->create(['user_id' => $user->user_id]);

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
                'message' => HandleCheckoutSessionCompletedJob::ERROR_MESSAGE_NO_ERROR,
            ])
        ;

        // cleaning
    }

    protected function mockStripe(): void
    {
        $this->stripeClient = $this->mock(StripeClient::class, function (MockInterface $mock): void {
            $mock->shouldReceive(config('app.stripe_secret'))->andReturnSelf();
            // is called like this : $stripeClient->subscriptions->retrieve(subId)
            $mock->shouldReceive('request')->with('get', '/v1/subscriptions/subId', null, null)->andReturn('{id:subId}');
        });
    }
}
