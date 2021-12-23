<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use App\User;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PlansAccessTest extends TestCase
{
    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function plan_upgrade_should_be_denied_to_guest(): void
    {
        $this->get(route('plans.index', $this->channel))
            ->assertRedirect('/login')
        ;
    }

    /** @test */
    public function plan_upgrade_should_be_denied_to_another_user(): void
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->get(route('plans.index', $this->channel))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function plan_upgrade_should_be_allowed_to_owner(): void
    {
        $this->actingAs($this->channel->user)
            ->get(route('plans.index', $this->channel))
            ->assertSuccessful()
            ->assertViewIs('plans.index')
        ;
    }
}
