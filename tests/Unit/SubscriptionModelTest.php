<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Channel;
use App\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class SubscriptionModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel */
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
        $this->assertEquals($this->channel->channel_id, Channel::byChannelId($this->channel->channel_id)->channel_id);
    }
}
