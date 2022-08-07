<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Exceptions\CannotIdentifyUserFromStripeException;
use App\Exceptions\ChannelOwnerMismatchingStripeException;
use App\Exceptions\EmptyChannelIdReceivedFromStripeException;
use App\Exceptions\EmptyCustomerReceivedFromStripeException;
use App\Exceptions\EmptyPlanReceivedFromStripeException;
use App\Exceptions\EmptySubscriptionReceivedFromStripeException;
use App\Exceptions\InvalidCustomerEmailReceivedFromStripeException;
use App\Exceptions\InvalidSubscriptionReceivedFromStripeException;
use App\Exceptions\UnknownChannelIdReceivedFromStripeException;
use App\Exceptions\UnknownStripePlanReceivedFromStripeException;
use App\Jobs\StripeWebhooks\HandleCheckoutSessionCompletedJob;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use InvalidArgumentException;
use Mockery\MockInterface;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Collection as StripeCollection;
use Stripe\Plan;
use Stripe\StripeClient;
use Stripe\Subscription;
use Stripe\SubscriptionItem;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HandleCheckoutSessionCompletedJobTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake();
        $this->channel = $this->createChannelWithPlan();
        $this->user = $this->channel->user;
    }

    /** @test */
    public function run_is_throwing_exception_when_user_is_missing(): void
    {
        $webHookCall = new WebhookCall([
            'payload' => [
                'data' => [
                    'object' => [
                    ],
                ],
            ],
        ]);
        $job = new HandleCheckoutSessionCompletedJob($webHookCall);
        $this->assertInstanceOf(HandleCheckoutSessionCompletedJob::class, $job);
        $this->expectException(EmptyCustomerReceivedFromStripeException::class);
        $job->handle();
    }

    /** @test */
    public function run_is_throwing_exception_when_email_invalid(): void
    {
        $webHookCall = new WebhookCall([
            'payload' => [
                'data' => [
                    'object' => [
                        'customer_email' => 'invalid email',
                    ],
                ],
            ],
        ]);
        $job = new HandleCheckoutSessionCompletedJob($webHookCall);
        $this->expectException(InvalidCustomerEmailReceivedFromStripeException::class);
        $job->handle();
    }

    /** @test */
    public function run_is_throwing_exception_when_email_unknown(): void
    {
        $webHookCall = new WebhookCall([
            'payload' => [
                'data' => [
                    'object' => [
                        'customer_email' => 'sarah@connor.com',
                    ],
                ],
            ],
        ]);
        $job = new HandleCheckoutSessionCompletedJob($webHookCall);
        $this->expectException(CannotIdentifyUserFromStripeException::class);
        $job->handle();
    }

    /** @test */
    public function run_is_throwing_exception_when_channel_id_missing(): void
    {
        $webHookCall = new WebhookCall([
            'payload' => [
                'data' => [
                    'object' => [
                        'customer_email' => $this->user->email,
                    ],
                ],
            ],
        ]);
        $job = new HandleCheckoutSessionCompletedJob($webHookCall);
        $this->expectException(EmptyChannelIdReceivedFromStripeException::class);
        $job->handle();
    }

    /** @test */
    public function run_is_throwing_exception_when_channel_id_unknown(): void
    {
        $webHookCall = new WebhookCall([
            'payload' => [
                'data' => [
                    'object' => [
                        'customer_email' => $this->user->email,
                        'metadata' => [
                            'channel_id' => 'unknown channel_id',
                        ],
                    ],
                ],
            ],
        ]);
        $job = new HandleCheckoutSessionCompletedJob($webHookCall);
        $this->expectException(UnknownChannelIdReceivedFromStripeException::class);
        $job->handle();
    }

    /** @test */
    public function run_is_throwing_exception_when_user_not_the_owner_of_channel(): void
    {
        $anotherChannel = Channel::factory()->create();
        $webHookCall = new WebhookCall([
            'payload' => [
                'data' => [
                    'object' => [
                        'customer_email' => $this->user->email,
                        'metadata' => [
                            'channel_id' => $anotherChannel->channel_id,
                        ],
                    ],
                ],
            ],
        ]);
        $job = new HandleCheckoutSessionCompletedJob($webHookCall);
        $this->expectException(ChannelOwnerMismatchingStripeException::class);
        $job->handle();
    }

    /** @test */
    public function run_is_throwing_exception_when_subscription_missing(): void
    {
        $webHookCall = new WebhookCall([
            'payload' => [
                'data' => [
                    'object' => [
                        'customer_email' => $this->user->email,
                        'metadata' => [
                            'channel_id' => $this->channel->channel_id,
                        ],
                    ],
                ],
            ],
        ]);
        $job = new HandleCheckoutSessionCompletedJob($webHookCall);
        $this->expectException(EmptySubscriptionReceivedFromStripeException::class);
        $job->handle();
    }

    /** @test */
    public function run_is_throwing_exception_when_subscription_unknown(): void
    {
        $invalidSubscriptionId = 'sub_unknown';
        $webHookCall = new WebhookCall([
            'payload' => [
                'data' => [
                    'object' => [
                        'customer_email' => $this->user->email,
                        'metadata' => [
                            'channel_id' => $this->channel->channel_id,
                        ],
                        'subscription' => $invalidSubscriptionId,
                    ],
                ],
            ],
        ]);

        $stripeCLient = $this->mock(StripeClient::class, function (MockInterface $mock) use ($invalidSubscriptionId): void {
            $mock->shouldReceive(config('app.stripe_secret'))->andReturnSelf();
            // is called like this : $stripeClient->subscriptions->retrieve(subId)
            $mock->shouldReceive('request')
                ->with('get', '/v1/subscriptions/' . $invalidSubscriptionId, null, null)
                ->andThrow(InvalidArgumentException::class, '')
            ;
        });

        $job = new HandleCheckoutSessionCompletedJob($webHookCall, $stripeCLient);
        $this->expectException(InvalidSubscriptionReceivedFromStripeException::class);
        $job->handle();
    }

    /** @test */
    public function run_is_throwing_exception_when_stripe_plan_empty(): void
    {
        $validSubscriptionId = 'sub_valid';
        $webHookCall = new WebhookCall([
            'payload' => [
                'data' => [
                    'object' => [
                        'customer_email' => $this->user->email,
                        'metadata' => [
                            'channel_id' => $this->channel->channel_id,
                        ],
                        'subscription' => $validSubscriptionId,
                    ],
                ],
            ],
        ]);

        $expectedSubscription = new Subscription($validSubscriptionId, [
            'object' => 'subscription',
            'plan' => [],
            'quantity' => 1,
        ]);

        $stripeCLient = $this->stripeMocked($expectedSubscription);

        $job = new HandleCheckoutSessionCompletedJob($webHookCall, $stripeCLient);
        $this->expectException(EmptyPlanReceivedFromStripeException::class);
        $job->handle();
    }

    /** @test */
    public function run_is_throwing_exception_when_stripe_plan_unknown(): void
    {
        $this->markTestIncomplete("For your mental health sake come back here when you'll be better at mocking stripe.");
        $validSubscriptionId = 'sub_valid';
        $webHookCall = new WebhookCall([
            'payload' => [
                'data' => [
                    'object' => [
                        'customer_email' => $this->user->email,
                        'metadata' => [
                            'channel_id' => $this->channel->channel_id,
                        ],
                        'subscription' => $validSubscriptionId,
                    ],
                ],
            ],
        ]);

        /**
         * my problem here is (actually) I cannot fake one subscription
         * WITH its plan relation. So I cannot do $subscription->plan->id because
         * it will always be null.
         * I tried to inject new Stripe\Plan() => failure
         * I tried to inject Stripe\Collection with Stripe\SubscriptionItem with Stripe\Plan => failure.
         */
        $unknownStripePlan = new Plan('unknown_stripe_plan_id');
        $subscriptionItem = new SubscriptionItem('fooSubItemId', ['plan' => $unknownStripePlan]);
        $stripeCollection = new StripeCollection('fooCollId', ['data' => $subscriptionItem]);
        $expectedSubscription = new Subscription($validSubscriptionId, [
            'object' => 'subscription',
            'plan' => $unknownStripePlan,
            'quantity' => 1,
            'items' => $stripeCollection,
        ]);

        $stripeCLient = $this->stripeMocked($expectedSubscription);

        $job = new HandleCheckoutSessionCompletedJob($webHookCall, $stripeCLient);
        $this->expectException(UnknownStripePlanReceivedFromStripeException::class);
        $job->handle();
    }

    /** @test */
    public function run_is_finally_fine_when_everything_ok(): void
    {
        $this->markTestIncomplete('Same thing as above.');

        $job = new HandleCheckoutSessionCompletedJob($webHookCall, $stripeCLient);
        $this->expectException(UnknownStripePlanReceivedFromStripeException::class);
        $job->handle();

        // assert channel has changed its subscription plan
    }

    /**
     * ===========================================================
     * Helpers
     * ===========================================================.
     *
     * @param mixed $expectedSubscription
     */
    protected function stripeMocked($expectedSubscription): MockInterface
    {
        return $this->mock(StripeClient::class, function (MockInterface $mock) use ($expectedSubscription): void {
            $mock->shouldReceive(config('app.stripe_secret'))->andReturnSelf();
            // is called like this : $stripeClient->subscriptions->retrieve(subId)
            $mock->shouldReceive('request')
                ->with('get', '/v1/subscriptions/' . $expectedSubscription->id, null, null)
                ->andReturn($expectedSubscription)
            ;
        });
    }
}
