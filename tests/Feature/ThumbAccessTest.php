<?php

namespace Tests\Feature;

use App\Events\ThumbUpdated;
use App\Modules\Vignette;
use App\Playlist;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ThumbAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Playlist $playlist */
    protected $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        $this->playlist = factory(Playlist::class)->create();
    }

    /** @test */
    public function editing_cover_is_denied_to_guests()
    {
        array_map(function ($routeToCheck) {
            $this->get(route($routeToCheck, $this->channel))
                ->assertRedirect(route('login'));
        }, [
            'channel.cover.edit',
            'playlist.cover.edit',
        ]);
    }

    /** @test */
    public function updating_cover_is_denied_to_guests()
    {
        array_map(function ($routeToCheck) {
            $this->patch(route($routeToCheck, $this->channel))
                ->assertRedirect(route('login'));
        }, [
            'channel.cover.update',
            'playlist.cover.update',
        ]);
    }

    /** @test */
    public function editing_channel_cover_is_denied_to_another_user()
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->get(route('channel.cover.edit', $this->channel))
            ->assertForbidden();
    }

    /** @test */
    public function editing_playlist_cover_is_denied_to_another_user()
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->get(route('playlist.cover.edit', $this->playlist))
            ->assertForbidden();
    }

    /** @test */
    public function updating_channel_cover_is_denied_to_another_user()
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->patch(route('channel.cover.update', $this->channel))
            ->dump()
            ->assertForbidden();
    }

    /** @test */
    public function updating_playlist_cover_is_denied_to_another_user()
    {
        $notTheOwner = factory(User::class)->create();
        $this->actingAs($notTheOwner)
            ->patch(route('playlist.cover.update', $this->playlist))
            ->assertForbidden();
    }

    public function testThumpIsUpdated()
    {
        Event::fake();

        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->post(route('channel.cover.update', $this->channel), [
                'new_thumb_file' => UploadedFile::fake()->image('photo1.jpg', 1400, 1400)
            ])
            ->assertSuccessful()
            ->assertSee(Vignette::fromThumb($this->channel->thumb)->url());

        Event::assertDispatched(ThumbUpdated::class);
    }
}
