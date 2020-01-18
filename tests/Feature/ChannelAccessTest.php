<?php

namespace Tests\Unit;

use App\User;
use App\Channel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function testChannelIndexIsNotPossibleForGuest()
    {
        $response = $this->get('/channel/');
        $response->assertRedirect('/login');
    }

    public function testChannelIndexIsAllowedToOwner()
    {
        $channel = factory(Channel::class)->create();
        $response = $this->actingAs($channel->user)->get('/channel/');
        $response->assertSuccessful();
        $response->assertViewIs('channel.index');
        $response->assertSeeText($channel->channel_name);
    }

    public function testChannelEditIsNotPossibleForGuest()
    {
        $channel = factory(Channel::class)->create();
        $response = $this->get('/channel/' . $channel->channelId().'/edit');
        $response->assertRedirect('/login');
    }

    public function testChannelEditIsForbidenForAnotherUser()
    {
        $channel = factory(Channel::class)->create();
        $forbiddenUser = factory(User::class)->create();
        $response = $this->actingAs($forbiddenUser)->get('/channel/' . $channel->channelId().'/edit');
        $response->assertForbidden();
    }

    public function testChannelEditIsAllowedToOwner()
    {
        $channel = factory(Channel::class)->create();
        $response = $this->actingAs($channel->user)->get('/channel/' . $channel->channelId().'/edit');
        $response->assertSuccessful();
        $response->assertViewIs('channel.edit');
    }

    public function testChannelViewIsNotPossibleForGuest()
    {
        $channel = factory(Channel::class)->create();
        $response = $this->get('/channel/' . $channel->channelId());
        $response->assertRedirect('/login');
    }

    public function testChannelViewIsForbidenForAnotherUser()
    {
        $channel = factory(Channel::class)->create();
        $forbiddenUser = factory(User::class)->create();
        $response = $this->actingAs($forbiddenUser)->get('/channel/' . $channel->channelId());
        $response->assertForbidden();
    }

    public function testChannelViewIsAllowedToOwner()
    {
        $channel = factory(Channel::class)->create();
        $response = $this->actingAs($channel->user)->get('/channel/' . $channel->channelId());
        $response->assertSuccessful();
        $response->assertViewIs('channel.show');
    }
}
