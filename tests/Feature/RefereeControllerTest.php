<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class RefereeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->verifiedAt(now())->create();
    }

    /** @test */
    public function user_with_no_referee_should_see_no_referees(): void
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('referees'))
            ->assertSuccessful()
            ->assertViewIs('referee.index')
            ->assertSeeTextInOrder([
                'My referees',
                'You have no referees at this time.',
            ])
        ;
    }

    /** @test */
    public function user_with_referees_should_see_them(): void
    {
        $starterPlan = Plan::factory()->name('starter')->create();
        $expectedNumberOfReferees = 10;
        $oneChannelUsers = User::factory($expectedNumberOfReferees)
            ->withReferrer($this->user)
            ->create()
            ->each(fn (User $user) => $this->createChannel($user, $starterPlan))
        ;

        $response = $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('referees'))
            ->assertSuccessful()
            ->assertViewIs('referee.index')
            ->assertSeeTextInOrder([
                'My referees',
                'Registered at',
                'Name',
                'Channel',
                'Plan subscribed',
                'Next payment',
            ])
        ;

        $oneChannelUsers->each(
            fn (User $user) => $response->assertSeeTextInOrder([
                $user->channel->channel_createdAt,
                $user->fullname,
                $user->channel->nameWithId(),
                $user->channel->plan->price,
                $nextPayment,
            ])
        );
    }

    /** @test */
    public function guest_should_be_redirected(): void
    {
        $this->get(route('referees'))
            ->assertRedirect('/login')
        ;
    }
}
