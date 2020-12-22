<?php

namespace Tests\Feature;

use App\Channel;
use App\Media;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediasAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
    }

    public function testForbidenForGuests()
    {
        array_map(function ($routeToCheck) {
            $this->get(route($routeToCheck, $this->channel))->assertRedirect(
                route('login')
            );
        }, [
            'channel.medias.index',
            'channel.medias.create',
        ]);
    }

    public function testForbiddenToAnotherUser()
    {
        $notTheOwner = factory(User::class)->create();
        $media = factory(Media::class)->create(['channel_id' => $this->channel->channel_id]);
        $this->actingAs($notTheOwner)
            ->get(route('channel.medias.edit', ['channel' => $this->channel, 'media' => $media]))
            ->assertForbidden();
        $this->actingAs($notTheOwner)
            ->get(route('channel.medias.show', [$this->channel, $media]))
            ->assertForbidden();
    }

    public function testAllowedForOwner()
    {
        array_map(
            function ($routeToCheck) {
                $this->actingAs($this->channel->user)
                    ->get(route($routeToCheck, $this->channel))
                    ->assertSuccessful();
            },
            [
                'channel.thumbs.index',
                'channel.thumbs.edit'
            ]
        );
    }
}
