<?php

namespace Tests\Feature;

use App\Channel;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class ChannelCreationIsPossible extends TestCase
{
    use RefreshDatabase;

    /** @var \App\User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
        $this->user = factory(User::class)->create(
            [
                'password' => bcrypt('i-love-laravel')
            ]
        );
    }

    public function testChannelCreationIsFine()
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->from(route('channel.create'))
            ->post(route('channel.store'), [
                "channel_url" => "https://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw",
            ])
            ->assertSuccessful()
            ->assertViewIs("home")
            ->assertSessionHasNoErrors()
            ->assertSeeText('has been successfully registered');
        $this->assertCount(1, Channel::all());
    }

    public function testTryCreatingWithInvalidChannelIdWillFail()
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->from(route('channel.create'))
            ->post(route('channel.store'), [
                "channel_url" => "https://www.youtube.com/channel/This-Will^never~exist",
            ])
            ->assertViewIs('channel.create')
            ->assertSessionHasNoErrors()
            ->assertSeeText('This channel url is invalid.');
        $this->assertCount(0, Channel::all());
    }

    public function testTryCreatingANonExistingChannelIdWillFail()
    {
        $invalidChannelId = 'invalidChannelId';
        $this->followingRedirects()
            ->actingAs($this->user)
            ->from(route('channel.create'))
            ->post(route('channel.store'), [
                "channel_url" => "https://www.youtube.com/channel/{$invalidChannelId}",
            ])
            ->assertViewIs('channel.create')
            ->assertSessionHasNoErrors()
            ->assertSeeText("This channel id {$invalidChannelId} does not exists on youtube.");
        $this->assertCount(0, Channel::all());
    }
}
