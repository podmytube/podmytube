<?php

namespace Tests\Feature;

use App\User;
use Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeAccessTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        $this->markTestSkipped('This test is failing because of strange relationship handling with sqlite');
        parent::setUp();
        Artisan::call('db:seed');
        $this->user = factory(User::class)->create();
    }

    public function testGuestIsRejected()
    {
        $response = $this->get(route('home'))->assertRedirect(route('login'));
    }

    public function testUserCanAccessHomeWithNoChannels()
    {
        $response = $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('home');
    }

    /**
     *  @todo fix this test
     */
    /*
    public function testUserShouldSeeHisChannel()
    {
        $channel = factory(Channel::class)->create([
            'user_id' => $this->user->userId(),
        ]);
        $response = $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('home'))
            ->dump()
            ->assertSuccessful()
            ->assertViewIs('home')
            ->assertSee($channel->channel_name);
    } */
}
