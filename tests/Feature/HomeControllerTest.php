<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use App\Plan;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\User */
    protected $user;

    /** @var \App\Channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function guest_should_be_rejected(): void
    {
        $this->get(route('home'))->assertRedirect(route('login'));
    }

    /** @test */
    public function auth_user_should_access_home(): void
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('home')
        ;
    }

    /** @test */
    public function user_should_see_his_channels(): void
    {
        // associating channel to user
        $this->channel->update(['user_id' => $this->user->id()]);

        /** adding another channel */
        $anotherChannel = Channel::factory()->create(['user_id' => $this->user->id()]);
        $anotherChannel->subscribeToPlan(Plan::factory()->create());

        $this->user->refresh();
        $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('home')
            ->assertSee(
                [
                    $this->channel->title(),
                    $anotherChannel->title(),
                ]
            )
        ;
    }
}
