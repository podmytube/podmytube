<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AnalyticsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function user_should_see_his_channel_analytics(): void
    {
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->get(route('analytics', $this->channel))
            ->assertSuccessful()
            ->assertViewIs('analytics.show')
            ->assertSeeText('Analytics')
        ;
    }

    /** @test */
    public function only_the_owner_should_see_his_channel_analytics(): void
    {
        /** @var Authenticatable $notTheOwner */
        $notTheOwner = User::factory()->create();
        $this->actingAs($notTheOwner)
            ->get(route('analytics', $this->channel))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function guest_should_be_redirected(): void
    {
        $this->get(route('analytics', $this->channel))->assertRedirect('/login');
    }
}
