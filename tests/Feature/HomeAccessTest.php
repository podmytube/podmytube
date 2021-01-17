<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        //$this->markTestSkipped('This test is failing because of strange relationship handling with sqlite');
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
    }

    public function testGuestIsRejected()
    {
        $response = $this->get(route('home'))->assertRedirect(route('login'));
    }

    public function testUserCanAccessHomeWithNoChannels()
    {
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('home');
    }

    public function testUserShouldSeeHisChannel()
    {
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('home')
            ->assertSee($this->channel->title());
    }
}
