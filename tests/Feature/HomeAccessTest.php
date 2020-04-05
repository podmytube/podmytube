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
        parent::setUp();
        Artisan::call('db:seed');
        $this->user = factory(User::class)->create();
    }

    public function testGuestIsRejected()
    {
        $response = $this->get('/home')->assertRedirect('/login');
    }

    public function testUserCanAccessHomeWithNoChannels()
    {
        $response = $this->followingRedirects()
            ->actingAs($this->user)
            ->get('/home')
            ->assertSuccessful()
            ->assertViewIs('home');
    }

    /* public function testUserShouldSeeHisChannel()
    {
        $channel = factory(Channel::class)->create(['user_id'=>$this->user->userId()]);
        dump(
            "user channel : ".$this->user->channels->first()->channel_name,
            "channel name : ".$channel->channel_name
        );
        dd();
        $response = $this->followingRedirects()
            ->actingAs($this->user)
            ->get('/home')
            ->assertSuccessful()
            ->assertViewIs('home')
            ->assertSee($channel->channel_name);
    } */
}
