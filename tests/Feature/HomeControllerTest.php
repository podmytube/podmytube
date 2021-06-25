<?php

namespace Tests\Feature;

use App\Channel;
use App\Plan;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\User $user² */
    protected $user;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function guest_should_be_rejected()
    {
        $this->get(route('home'))->assertRedirect(route('login'));
    }

    /** @test */
    public function auth_user_should_access_home()
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('home');
    }

    /** @test */
    public function user_should_see_his_channels()
    {
        /** associating channel to user */
        $this->channel->update(['user_id' => $this->user->id()]);

        /** adding another channel */
        $anotherChannel = factory(Channel::class)->create(['user_id' => $this->user->id()]);
        $anotherChannel->subscribeToPlan(factory(Plan::class)->create());

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
            );
    }
}
