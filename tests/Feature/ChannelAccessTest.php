<?php

namespace Tests\Feature;

use App\User;
use App\Channel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function testChannelIndexIsNotPossibleForGuest()
    {
        $this->get(route('channel.index'))->assertRedirect('/login');
    }

    public function testChannelIndexIsAllowedToOwnerAndHasAllItsChannel()
    {
        $channels = factory(Channel::class, 3)->create([
            'user_id' => $this->user->userId(),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('channel.index'))
            ->assertSuccessful()
            ->assertViewIs('channel.index');

        foreach ($channels as $channel) {
            $response->assertSeeText($channel->title());
        }
    }
    public function testChannelEditIsNotPossibleForGuest()
    {
        $channel = factory(Channel::class)->create();
        $this->get(route('channel.edit', $channel))->assertRedirect('/login');
    }

    public function testChannelEditIsForbidenForAnotherUser()
    {
        $channel = factory(Channel::class)->create();
        $forbiddenUser = factory(User::class)->create();
        $this->actingAs($forbiddenUser)
            ->get(route('channel.edit', $channel))
            ->assertForbidden();
    }

    public function testChannelEditIsAllowedToOwner()
    {
        $channel = factory(Channel::class)->create();
        $this->actingAs($channel->user)
            ->get(route('channel.edit', $channel))
            ->assertSuccessful()
            ->assertViewIs('channel.edit');
    }

    public function testChannelViewIsNotPossibleForGuest()
    {
        $channel = factory(Channel::class)->create();
        $this->get(route('channel.show', $channel))->assertRedirect('/login');
    }

    public function testChannelViewIsForbidenForAnotherUser()
    {
        $channel = factory(Channel::class)->create();
        $forbiddenUser = factory(User::class)->create();
        $this->actingAs($forbiddenUser)
            ->get(route('channel.show', $channel))
            ->assertForbidden();
    }

    public function testChannelViewIsAllowedToOwner()
    {
        $channel = factory(Channel::class)->create();
        $this->actingAs($channel->user)
            ->get(route('channel.show', $channel))
            ->assertSuccessful()
            ->assertViewIs('channel.show');
    }
}
