<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class MediasControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @var \App\Channel */
    protected $channel;

    /** @var \App\Media */
    protected $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannel();
        $this->media = $this->addMediasToChannel($this->channel);
    }

    /** @test */
    public function forbidden_for_guests(): void
    {
        $roadsToCheck = [
            'index' => 'get',
            'create' => 'get',
            'store' => 'post',
            'destroy' => 'delete',
            'update' => 'patch',
            'edit' => 'get',
        ];
        array_map(
            function ($roadToCheck, $method) {
                if (in_array($roadToCheck, ['destroy', 'update', 'edit'])) {
                    $this->{$method}(route('channel.medias.' . $roadToCheck, ['channel' => $this->channel, 'media' => $this->media]))->assertRedirect(route('login'));

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
        $notTheOwner = factory(User::class)->create();
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
    public function non_paying_channel_should_not_add_exclusive_content(): void
    {
        $plan = $this->getFreePlan();
        $this->channel = $this->createChannelWithPlan($plan);
        $this->actingAs($this->channel->user)
            ->get(route('channel.medias.create', $this->channel))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function paying_channel_may_add_exclusive_content(): void
    {
        $plan = $this->getPlanBySlug('daily_youtuber');
        $this->channel = $this->createChannelWithPlan($plan);
        $this->actingAs($this->channel->user)
            ->get(route('channel.medias.create', $this->channel))
            ->assertSuccessful()
        ;
    }

    /** @test */
    public function non_paying_channel_should_not_edit_exclusive_content(): void
    {
        $plan = $this->getFreePlan();
        $this->channel = $this->createChannelWithPlan($plan);
        $this->actingAs($this->channel->user)
            ->get(route('channel.medias.edit', ['channel' => $this->channel, 'media' => $this->media]))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function paying_channel_may_edit_exclusive_content(): void
    {
        $plan = $this->getPlanBySlug('daily_youtuber');
        $this->channel = $this->createChannelWithPlan($plan);
        $this->actingAs($this->channel->user)
            ->get(route('channel.medias.edit', ['channel' => $this->channel, 'media' => $this->media]))
            ->assertSuccessful()
        ;
    }

    /** @test */
    public function non_paying_channel_should_not_store_exclusive_content(): void
    {
        $plan = $this->getFreePlan();
        $this->channel = $this->createChannelWithPlan($plan);

        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->post(route('channel.medias.store', ['channel' => $this->channel]), $this->mediaCreateFields())
            ->assertForbidden()
        ;
    }

    /** @test */
    public function paying_channel_may_store_exclusive_content(): void
    {
        $plan = $this->getPlanBySlug('daily_youtuber');
        $this->channel = $this->createChannelWithPlan($plan);

        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->post(route('channel.medias.store', ['channel' => $this->channel]), $this->mediaCreateFields())
            ->assertSuccessful()
        ;
    }

    /** @test */
    public function non_paying_channel_should_not_update_exclusive_content(): void
    {
        $this->markTestIncomplete('TO BE DONE');
        
        $plan = $this->getFreePlan();
        $this->channel = $this->createChannelWithPlan($plan);

        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->patch(route('channel.medias.update', ['channel' => $this->channel, 'media' => $this->media]), $this->mediaCreateFields())
            ->assertForbidden()
        ;
    }

    /** @test */
    public function paying_channel_may_update_exclusive_content(): void
    {
        $this->markTestIncomplete('TO BE DONE');
    }

    /**
     * ===================================================================
     * helpers
     * ===================================================================.
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
}
