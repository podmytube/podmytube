<?php

namespace Tests\Feature;

use App\Events\ThumbUpdated;
use App\Listeners\UploadPodcast;
use App\Modules\Vignette;
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

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
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

    public function testThumpIsUpdated()
    {
        Event::fake();

        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->post(route('channel.thumbs.store', $this->channel), [
                'new_thumb_file' => UploadedFile::fake()->image('photo1.jpg', 1400, 1400)
            ])
            ->assertSuccessful()
            ->assertSee(Vignette::fromThumb($this->channel->thumb)->url());

        Event::assertDispatched(ThumbUpdated::class);
    }

    
}
