<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\ThumbUpdatedEvent;
use App\Jobs\CreateVignetteFromThumbJob;
use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Thumb;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ThumbsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Channel $channel;
    protected Playlist $playlist;

    public function setUp(): void
    {
        parent::setUp();
        Queue::fake(CreateVignetteFromThumbJob::class);
        Event::fake(ThumbUpdatedEvent::class);
        $this->user = User::factory()->create();
        $this->channel = $this->createChannel($this->user);
        $this->playlist = Playlist::factory()->create(['channel_id' => $this->channel->channelId()]);
    }

    /** @test */
    public function editing_cover_is_denied_to_guests(): void
    {
        array_map(function ($routeToCheck): void {
            $this->get(route($routeToCheck, $this->channel))
                ->assertRedirect(route('login'))
            ;
        }, [
            'channel.cover.edit',
            'playlist.cover.edit',
        ]);
    }

    /** @test */
    public function updating_cover_is_denied_to_guests(): void
    {
        array_map(function ($routeToCheck): void {
            $this->patch(route($routeToCheck, $this->channel))
                ->assertRedirect(route('login'))
            ;
        }, [
            'channel.cover.update',
            'playlist.cover.update',
        ]);
    }

    /** @test */
    public function editing_channel_cover_is_denied_to_another_user(): void
    {
        /** @var Authenticatable $notTheOwner */
        $notTheOwner = User::factory()->create();
        $this->actingAs($notTheOwner)
            ->get(route('channel.cover.edit', $this->channel))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function editing_playlist_cover_is_denied_to_another_user(): void
    {
        /** @var Authenticatable $notTheOwner */
        $notTheOwner = User::factory()->create();
        $this->actingAs($notTheOwner)
            ->get(route('playlist.cover.edit', $this->playlist))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function updating_channel_cover_is_denied_to_another_user(): void
    {
        /** @var Authenticatable $notTheOwner */
        $notTheOwner = User::factory()->create();
        $this->followingRedirects()
            ->actingAs($notTheOwner)
            ->patch(
                route('channel.cover.update', $this->channel),
                [
                    'new_thumb_file' => UploadedFile::fake()->image('photo1.jpg', 1400, 1400),
                ]
            )
            ->assertForbidden()
        ;
        // ->assertRedirect(route(''));
    }

    /** @test */
    public function updating_playlist_cover_is_denied_to_another_user(): void
    {
        /** @var Authenticatable $notTheOwner */
        $notTheOwner = User::factory()->create();
        $this->actingAs($notTheOwner)
            ->patch(route('playlist.cover.update', $this->playlist), [
                'new_thumb_file' => UploadedFile::fake()->image('photo1.jpg', 1400, 1400),
            ])
            ->assertForbidden()
        ;
    }

    /** @test */
    public function channel_thumb_should_be_updated(): void
    {
        $this->assertNull($this->channel->cover);

        // updating cover should be ok and displayed on home
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->from(route('home'))
            ->patch(route('channel.cover.update', $this->channel), [
                'new_thumb_file' => UploadedFile::fake()->image('photo1.jpg', 1400, 1400),
            ])
            ->assertSuccessful()
        ;

        Queue::assertPushedOn('podwww', CreateVignetteFromThumbJob::class);
        Event::assertDispatched(ThumbUpdatedEvent::class);

        // once updated, coverable should have a cover
        $this->channel->refresh();
        $this->assertNotNull($this->channel->cover);
        $this->assertInstanceOf(Thumb::class, $this->channel->cover);
    }

    /** @test */
    public function playlist_thumb_should_be_updated(): void
    {
        $this->assertNull($this->playlist->cover);

        // updating cover should be ok and displayed on home
        $this->followingRedirects()
            ->actingAs($this->playlist->owner())
            ->from(route('home'))
            ->patch(route('playlist.cover.update', $this->playlist), [
                'new_thumb_file' => UploadedFile::fake()->image('photo1.jpg', 1400, 1400),
            ])
            ->assertSuccessful()
        ;

        Queue::assertPushedOn('podwww', CreateVignetteFromThumbJob::class);
        Event::assertDispatched(ThumbUpdatedEvent::class);

        // once updated, coverable should have a cover
        $this->playlist->refresh();
        $this->assertNotNull($this->playlist->cover);
        $this->assertInstanceOf(Thumb::class, $this->playlist->cover);
    }
}
