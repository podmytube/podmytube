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
class MediasControllerTest extends TestCase
{
    use RefreshDatabase;

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
    public function forbiden_for_guests(): void
    {
        $roadsToCheck = [
            'index' => 'get',
            'create' => 'get',
            'store' => 'post',
            'destroy' => 'delete',
            'update' => 'patch',
            'edit' => 'delete',
        ];
        array_map(
            function ($roadToCheck, $method) {
                if (in_array($method, ['delete'])) {
                    $this->{$method}(route('channel.medias.'.$roadToCheck, ['channel' => $this->channel, 'media' => $this->media]))->assertRedirect(route('login'));

                    return true;
                }
                $this->{$method}(route($roadToCheck, $this->channel))->assertRedirect(route('login'));
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
    public function paying_channel_may_edit_exclusive_content(): void
    {
        $plan = $this->getPlanBySlug('daily_youtuber');
        $this->channel = $this->createChannelWithPlan($plan);
        $this->actingAs($this->channel->user)
            ->get(route('channel.medias.edit', ['channel' => $this->channel, 'media' => $this->media]))
            ->assertSuccessful()
        ;
    }
}
