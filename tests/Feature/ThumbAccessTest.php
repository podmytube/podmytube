<?php

namespace Tests\Feature;

use App\Channel;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThumbAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
    }

    public function testThumbAreForbidenForGuests()
    {
        array_map(function ($routeToCheck) {
            $this->get(route($routeToCheck, $this->channel))->assertRedirect(
                route('login')
            );
        }, [
            'channel.thumbs.index',
            'channel.thumbs.edit',
            'channel.thumbs.store',
            'channel.thumbs.update',
        ]);
    }

    public function testEditThumbIsRefusedToAnotherUser()
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->get(route('channel.thumbs.edit', $this->channel))
            ->assertForbidden();
    }

    public function testEditIndexAreAllowedForOwner()
    {
        array_map(
            function ($routeToCheck) {
                $this->actingAs($this->channel->user)
                    ->get(route($routeToCheck, $this->channel))
                    ->assertSuccessful();
            },
            ['channel.thumbs.index', 'channel.thumbs.edit']
        );
    }
}
