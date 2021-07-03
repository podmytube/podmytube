<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use App\Media;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class MediasAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel */
    protected $channel;

    /** @var \App\Media */
    protected $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
        $this->media = factory(Media::class)->create(['channel_id' => $this->channel->channel_id]);
    }

    public function test_forbiden_for_guests(): void
    {
        array_map(function ($routeToCheck): void {
            $this->get(route($routeToCheck, $this->channel))->assertRedirect(
                route('login')
            );
        }, [
            'channel.medias.index',
            'channel.medias.create',
        ]);
    }

    public function test_media_edit_forbidden_to_another_user(): void
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->get(route('channel.medias.edit', ['channel' => $this->channel, 'media' => $this->media]))
            ->assertForbidden()
        ;
    }

    public function test_allowed_for_owner(): void
    {
        $this->actingAs($this->channel->user)
            ->get(route('channel.cover.edit', $this->channel))
            ->assertSuccessful()
        ;
    }
}
