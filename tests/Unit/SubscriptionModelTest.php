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
 * @coversNothing
 */
class SubscriptionModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\Channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
        $this->channel = $this->createChannelWithPlan(Plan::find(Plan::FREE_PLAN_ID));
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
