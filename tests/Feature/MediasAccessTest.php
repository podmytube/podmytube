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

    /** @var \App\Media $media */
    protected $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
        $this->media = factory(Media::class)->create(['channel_id' => $this->channel->channel_id]);
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

    public function testMediaEditForbiddenToAnotherUser()
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->get(route('channel.medias.edit', ['channel' => $this->channel, 'media' => $this->media]))
            ->assertForbidden();
    }

    public function testAllowedForOwner()
    {
        $this->actingAs($this->channel->user)
            ->get(route('channel.cover.edit', $this->channel))
            ->assertSuccessful();
    }
}
