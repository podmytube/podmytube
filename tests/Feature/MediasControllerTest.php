<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\MediaUploadedByUser;
use App\Events\PodcastUpdated;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MediasControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected Channel $channel;
    protected Media $media;
    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannel();
        $this->media = $this->addMediasToChannel($this->channel);
        $this->starterPlan = Plan::factory()->name('starter')->create();
    }

    /** @test */
    public function checking_routes_are_alive(): void
    {
        $roadsToCheck = [
            'index' => 'get',
            'create' => 'get',
            'store' => 'post',
            'update' => 'patch',
            'edit' => 'get',
        ];
        array_map(
            function ($roadToCheck, $method) {
                if (in_array($roadToCheck, ['destroy', 'update', 'edit'])) {
                    $this->{$method}(route('channel.medias.' . $roadToCheck, ['channel' => $this->channel, 'media' => $this->media]))
                        ->assertRedirect(route('login'))
                    ;

                    return true;
                }
                $this->{$method}(route('channel.medias.' . $roadToCheck, $this->channel))->assertRedirect(route('login'));
            },
            array_keys($roadsToCheck),
            $roadsToCheck,
        );
    }

    /** @test */
    public function index_forbidden_to_another_user(): void
    {
        $notTheOwner = User::factory()->create();
        $this->actingAs($notTheOwner)
            ->get(route('channel.medias.index', $this->channel))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function index_allowed_to_owner(): void
    {
        $this->actingAs($this->channel->user)
            ->get(route('channel.medias.index', $this->channel))
            ->assertSuccessful()
        ;
    }

    /** @test */
    public function paying_channel_may_add_exclusive_content(): void
    {
        $this->channel = $this->createChannelWithPlan($this->starterPlan);
        $this->actingAs($this->channel->user)
            ->get(route('channel.medias.create', $this->channel))
            ->assertSuccessful()
        ;
    }

    /** @test */
    public function paying_channel_may_edit_exclusive_content(): void
    {
        $this->channel = $this->createChannelWithPlan($this->starterPlan);
        $this->actingAs($this->channel->user)
            ->get(route('channel.medias.edit', ['channel' => $this->channel, 'media' => $this->media]))
            ->assertSuccessful()
        ;
    }

    /** @test */
    public function paying_channel_may_store_exclusive_content(): void
    {
        $this->channel = $this->createChannelWithPlan($this->starterPlan);

        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->post(route('channel.medias.store', ['channel' => $this->channel]), $this->mediaCreateFields())
            ->assertSuccessful()
        ;
    }

    /** @test */
    public function update_without_media_should_be_fine(): void
    {
        Event::fake();
        $this->channel = $this->createChannelWithPlan($this->starterPlan);

        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->patch(route('channel.medias.update', ['channel' => $this->channel, 'media' => $this->media]), $this->mediaUpdateFields(false))
            ->assertSuccessful()
        ;

        $this->media->refresh();
        $this->assertEquals('title updated', $this->media->title);
        $this->assertEquals('description updated', $this->media->description);
        Event::assertDispatched(MediaUploadedByUser::class);
    }

    public function update_with_media_should_be_fine(): void
    {
        Event::fake();
        $this->channel = $this->createChannelWithPlan($this->starterPlan);

        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->patch(route('channel.medias.update', ['channel' => $this->channel, 'media' => $this->media]), $this->mediaUpdateFields(true))
            ->assertSuccessful()
        ;

        $this->media->refresh();
        $this->assertEquals('title updated', $this->media->title);
        $this->assertEquals('description updated', $this->media->description);
        Event::assertDispatched(MediaUploadedByUser::class);
    }

    /** @test */
    public function disable_route_is_fine(): void
    {
        $this->channel = $this->createChannelWithPlan(Plan::bySlug('starter'));

        /** @var Media $mediaToDelete */
        $mediaToDelete = Media::factory()->for($this->channel)->create();

        Event::fake();
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->patch(route('media.disable', ['media' => $mediaToDelete]))
            ->assertSuccessful()
            ->assertSessionHasNoErrors()
            ->assertViewIs('medias.index')
        ;

        $mediaToDelete->refresh();
        // should have been deleted today
        $this->assertEquals(now()->toDateString(), $mediaToDelete->deleted_at->toDateString());
        Event::assertDispatched(fn (PodcastUpdated $event) => $event->podcastable->channelId() === $this->channel->channel_id);
    }

    /** @test */
    public function enable_route_is_fine(): void
    {
        $this->channel = $this->createChannelWithPlan(Plan::bySlug('starter'));

        /** @var Media $mediaToDelete */
        $mediaToDelete = Media::factory()->for($this->channel)->create();
        $mediaId = $mediaToDelete->id;
        $mediaToDelete->delete();

        Event::fake();
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->patch(route('media.enable', ['media' => $mediaId]))
            ->assertSuccessful()
            ->assertSessionHasNoErrors()
            ->assertViewIs('medias.index')
        ;

        $mediaToDelete->refresh();
        // should have been deleted today
        $this->assertNull($mediaToDelete->deleted_at);
        Event::assertDispatched(fn (PodcastUpdated $event) => $event->podcastable->channelId() === $this->channel->channel_id);
    }

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */
    protected function mediaCreateFields(): array
    {
        /** copying fixture/stub file with test data in tmp dir (where it should be uploaded) */
        $filename = 'lU_c9mku8JU.mp3';
        $fixturePath = base_path('tests/Fixtures/Audio/' . $filename);
        $tmpPath = sys_get_temp_dir() . '/' . $filename;
        copy($fixturePath, $tmpPath);

        /** creating real fake file */
        $uploadedFile = new UploadedFile($fixturePath, $filename, 'audio/mpeg', null, true);

        return [
            'channel' => $this->channel,
            'title' => $this->faker->word(),
            'description' => <<<'EOT'
Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, 
when an unknown printer took a galley of type and scrambled it to make a type 
specimen book. It has survived not only five centuries, but also the leap 
into electronic typesetting, remaining essentially unchanged. It was 
popularised in the 1960s with the release of Letraset sheets containing 
Lorem Ipsum passages, and more recently with desktop publishing software 
like Aldus PageMaker including versions of Lorem Ipsum.
EOT,
            'media_file' => $uploadedFile,
        ];
    }

    protected function mediaUpdateFields(bool $withMedia = false)
    {
        $params = [
            'channel' => $this->channel,
            'title' => 'title updated',
            'description' => 'description updated',
        ];

        if ($withMedia) {
        }

        return $params;
    }
}
