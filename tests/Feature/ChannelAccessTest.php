<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
    }

    public function testChannelIndexIsNotPossibleForGuest()
    {
        $this->get(route('channel.index'))->assertRedirect('/login');
    }

    public function testUserShouldSeeAllHisChannel()
    {
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('home')
            ->assertSeeText($this->channel->title());
    }

    public function testChannelEditIsNotPossibleForGuest()
    {
        $this->get(route('channel.edit', $this->channel))->assertRedirect('/login');
    }

    public function testChannelEditIsForbidenForAnotherUser()
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->get(route('channel.edit', $this->channel))
            ->assertForbidden();
    }

    public function testChannelEditIsAllowedToOwner()
    {
        $this->actingAs($this->channel->user)
            ->get(route('channel.edit', $this->channel))
            ->assertSuccessful()
            ->assertViewIs('channel.edit');
    }

    public function testChannelViewIsNotPossibleForGuest()
    {
        $this->get(route('channel.show', $this->channel))->assertRedirect('/login');
    }

    public function testChannelViewIsForbidenForAnotherUser()
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->get(route('channel.show', $this->channel))
            ->assertForbidden();
    }

    public function testChannelViewIsAllowedToOwner()
    {
        $this->actingAs($this->channel->user)
            ->get(route('channel.show', $this->channel))
            ->assertSuccessful()
            ->assertViewIs('channel.show');
    }
}
