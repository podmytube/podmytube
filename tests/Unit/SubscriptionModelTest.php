<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SubscriptionModelTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        $plan = Plan::factory()->isFree()->create();
        $this->channel = $this->createChannelWithPlan($plan);
    }

    /** @test */
    public function by_channel_id_is_ok(): void
    {
        $this->assertNull(Channel::byChannelId('this_will_never_exists'));

        $result = Subscription::byChannelId($this->channel->channel_id);
        $this->assertNotNull($result);
        $this->assertInstanceOf(Subscription::class, $result);
        $this->assertEquals($this->channel->channel_id, $result->channel_id);
    }
}
