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
        $response = $this->get('/channel/');
        $response->assertRedirect('/login');
    }

    public function testChannelIndexIsAllowedToOwnerAndHasAllItsChannel()
    {
        $channels = factory(Channel::class, 3)->create(['user_id' => $this->user->userId()]);

        $response = $this->actingAs($this->user)->get('/channel/');
        $response->assertSuccessful();
        $response->assertViewIs('channel.index');

        foreach ($channels as $channel) {
            $response->assertSeeText($channel->title());
        }
    }

    public function testChannelEditIsNotPossibleForGuest()
    {
        $channel = factory(Channel::class)->create();
        $response = $this->get('/channel/' . $channel->channelId() . '/edit');
        $response->assertRedirect('/login');
    }

    public function testChannelEditIsForbidenForAnotherUser()
    {
        $channel = factory(Channel::class)->create();
        $forbiddenUser = factory(User::class)->create();
        $response = $this->actingAs($forbiddenUser)->get('/channel/' . $channel->channelId() . '/edit');
        $response->assertForbidden();
    }

    public function testChannelEditIsAllowedToOwner()
    {
        $channel = factory(Channel::class)->create();
        $response = $this->actingAs($channel->user)->get('/channel/' . $channel->channelId() . '/edit');
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
