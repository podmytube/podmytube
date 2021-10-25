<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use App\Exceptions\CannotIdentifyUserFromStripeException;
use App\Exceptions\ChannelOwnerMismatchingStripeException;
use App\Exceptions\EmptyChannelIdReceivedFromStripeException;
use App\Exceptions\EmptySubscriptionReceivedFromStripeException;
use App\Exceptions\InvalidSubscriptionReceivedFromStripeException;
use App\Exceptions\UnknownChannelIdReceivedFromStripeException;
use App\Jobs\StripeWebhooks\HandleCheckoutSessionCompleted;
use App\StripePlan;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Stripe\Stripe;
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

    public function setUp(): void
    {
        parent::setUp();
        // setting signature check to false. I only need to check my part
        config(['stripe-webhooks.verify_signature' => false]);
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
                'message' => HandleCheckoutSessionCompleted::ERROR_MESSAGE_CUSTOMER_NOT_FOUND,
                'exception' => CannotIdentifyUserFromStripeException::class,
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
                'message' => HandleCheckoutSessionCompleted::ERROR_MESSAGE_EMPTY_CHANNEL,
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
                'message' => HandleCheckoutSessionCompleted::ERROR_MESSAGE_CHANNEL_NOT_FOUND,
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
                'message' => HandleCheckoutSessionCompleted::ERROR_MESSAGE_USER_IS_NOT_OWNER,
                'exception' => ChannelOwnerMismatchingStripeException::class,
            ])
        ;
    }

    /** @test */
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
                'message' => HandleCheckoutSessionCompleted::ERROR_MESSAGE_EMPTY_SUBSCRIPTION,
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
                'message' => HandleCheckoutSessionCompleted::ERROR_MESSAGE_SUBSCRIPTION_NOT_FOUND,
                'exception' => InvalidSubscriptionReceivedFromStripeException::class,
            ])
        ;
    }

    /** @test */
    public function complete_post_should_succeed(): void
    {
        $this->seedStripePlans();

        $stripeSubscriptionId = StripePlan::stripeIdsOnly()->random();
        $channel = $this->createChannelWithPlan();
        $stripeMocked = Mockery::mock(Stripe::class)->makePartial();
        $stripeMocked->shouldReceive('retrieve')->with($stripeSubscriptionId)->once()->andReturn(true);
        $orderNumber = $mocked->CREATION_COMMANDE($this->orderDataset);
        $this->postJson(
            self::STRIPE_ROUTE,
            [
                'type' => 'checkout.session.completed',
                'data' => [
                    'object' => [
                        'customer_email' => $channel->user->email,
                        'subscription' => $stripeSubscriptionId,
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

        /* $mocked->shouldReceive('create')->with(sldconfig('sld_domain_url'), sldconfig('sld_partenaire_id'));
        $mocked->shouldReceive('CREATION_COMMANDE')->with($this->orderDataset)->once()->andReturn(25457);
        $orderNumber = $mocked->CREATION_COMMANDE($this->orderDataset); */
    }
}
