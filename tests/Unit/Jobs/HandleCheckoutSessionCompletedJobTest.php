<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Channel;
use App\Exceptions\CannotIdentifyUserFromStripeException;
use App\Exceptions\ChannelOwnerMismatchingStripeException;
use App\Exceptions\EmptyChannelIdReceivedFromStripeException;
use App\Exceptions\EmptyCustomerReceivedFromStripeException;
use App\Exceptions\EmptySubscriptionReceivedFromStripeException;
use App\Exceptions\InvalidCustomerEmailReceivedFromStripeException;
use App\Exceptions\InvalidSubscriptionReceivedFromStripeException;
use App\Exceptions\UnknownChannelIdReceivedFromStripeException;
use App\Jobs\StripeWebhooks\HandleCheckoutSessionCompletedJob;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Mockery\MockInterface;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\StripeClient;
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
        $anotherChannel = factory(Channel::class)->create();
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
        $webHookCall = new WebhookCall([
            'payload' => [
                'data' => [
                    'object' => [
                        'customer_email' => $this->user->email,
                        'metadata' => [
                            'channel_id' => $this->channel->channel_id,
                        ],
                        'subscription' => 'sub_unknown',
                    ],
                ],
            ],
        ]);

        $job = new HandleCheckoutSessionCompletedJob($webHookCall, $this->stripeMocked());
        $this->expectException(InvalidSubscriptionReceivedFromStripeException::class);
        $job->handle();
    }

    protected function stripeMocked(): MockInterface
    {
        return $this->mock(StripeClient::class, function (MockInterface $mock): void {
            $mock->shouldReceive(config('app.stripe_secret'))->andReturnSelf();
            // is called like this : $stripeClient->subscriptions->retrieve(subId)
            $mock->shouldReceive('request')->with('get', '/v1/subscriptions/subId', null, null)->andReturn('{id:subId}');
        });
    }
}
