<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ChannelAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function user_should_see_all_his_channel(): void
    {
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('home')
            ->assertSeeText($this->channel->title())
        ;
    }

    /** @test */
    public function channel_edit_is_not_possible_for_guest(): void
    {
        $this->get(route('channel.edit', $this->channel))->assertRedirect('/login');
    }

    /** @test */
    public function channel_edit_is_forbiden_for_another_user(): void
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->get(route('channel.edit', $this->channel))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function channel_edit_is_allowed_to_owner(): void
    {
        $this->actingAs($this->channel->user)
            ->get(route('channel.edit', $this->channel))
            ->assertSuccessful()
            ->assertViewIs('channel.edit')
        ;
    }

    /** @test */
    public function channel_view_is_not_possible_for_guest(): void
    {
        $this->get(route('channel.show', $this->channel))->assertRedirect('/login');
    }

    /** @test */
    public function channel_view_is_forbiden_for_another_user(): void
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->get(route('channel.show', $this->channel))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function channel_view_is_allowed_to_owner(): void
    {
        $this->actingAs($this->channel->user)
            ->get(route('channel.show', $this->channel))
            ->assertSuccessful()
            ->assertViewIs('channel.show')
        ;
    }
}
