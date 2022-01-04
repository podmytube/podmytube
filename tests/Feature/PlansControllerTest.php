<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use App\Plan;
use App\User;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PlansControllerTest extends TestCase
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
    public function default_plan_upgrade_should_be_allowed_to_owner(): void
    {
        $this->seedStripePlans();
        /**
         * user should see 3 plans (starter, professionnal and business).
         * with monthly subscription.
         */
        $plans = Plan::bySlugs(['starter', 'professional', 'business']);
        $this->actingAs($this->channel->user)
            ->get(route('plans.index', $this->channel))
            ->assertSuccessful()
            ->assertViewIs('plans.index')
            ->assertSeeInOrder([
                $plans[0]->name,
                $plans[0]->price,
                '/mo',
                $plans[1]->name,
                $plans[1]->price,
                '/mo',
                $plans[2]->name,
                $plans[2]->price,
                '/mo',
            ])
        ;
    }

    /** @test */
    public function yearly_plan_upgrade_should_be_allowed_to_owner(): void
    {
        $this->seedStripePlans();
        /**
         * user should see 3 plans (starter, professionnal and business).
         * with yearly subscription.
         */
        $plans = Plan::bySlugs(['starter', 'professional', 'business']);
        $this->actingAs($this->channel->user)
            ->get(route('plans.index', ['channel' => $this->channel, 'yearly' => true]))
            ->assertSuccessful()
            ->assertViewIs('plans.index')
            ->assertSeeInOrder([
                $plans[0]->name,
                $plans[0]->price,
                '/yr',
                $plans[1]->name,
                $plans[1]->price,
                '/yr',
                $plans[2]->name,
                $plans[2]->price,
                '/yr',
            ])
        ;
    }
}
