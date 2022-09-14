<?php

declare(strict_types=1);

namespace Tests\Unit;

use Mockery\MockInterface;
use Stripe\StripeClient;
use Stripe\Subscription;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PlayingWithMockeryTest extends TestCase
{
    /** @test */
    public function it_should_give_us_a_cat(): void
    {
        $stripeClient = $this->mock(StripeClient::class, function (MockInterface $mock): void {
            $mock->shouldReceive(config('app.stripe_secret'))->andReturnSelf();
            // is called like this : $stripeClient->subscriptions->retrieve(subId)
            $mock->shouldReceive('request')->with('get', '/v1/subscriptions/subId', null, null)->andReturn('{id:subId}');
        });
        $this->assertInstanceOf(StripeClient::class, $stripeClient);

        $result = new StripeCLient(config('app.stripe_secret'));
        $this->assertNotNull($result);
        $this->assertInstanceOf(StripeClient::class, $result);

        $this->assertEquals('{id:subId}', $stripeClient->subscriptions->retrieve('subId'));

        $subscription = $this->mock(Subscription::class, function (MockInterface $mock): void {
            $mock->shouldReceive('retrieve')->with('cat')->once()->andReturn('dog');
        });

        $this->assertEquals('dog', $subscription->retrieve('cat'));
    }
}
