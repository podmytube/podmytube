<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\ApiKey;
use App\Models\Channel;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\IsFakingYoutube;

/**
 * @internal
 *
 * @coversNothing
 */
class UpdateChannelCommandTest extends TestCase
{
    use IsFakingYoutube;
    use RefreshDatabase;

    protected const DEFAULT_USER_ID = 1;
    protected const DEFAULT_PLAN_ID = 1;

    protected Channel $channel;
    protected string $playlistId;

    public function setUp(): void
    {
        parent::setUp();
        // because we need one (the 1 at least)
        ApiKey::factory()->create();

        $this->starterPlan = Plan::factory()->name('starter')->create();
        $this->channel = $this->createChannelWithPlan($this->starterPlan);
        $this->playlistId = $this->getPlaylistIdFromChannelId($this->channel->youtube_id);
    }

    /** @test */
    public function unknown_channel_id_should_fail(): void
    {
        $this->fakeEmptyChannelResponse();
        $this->artisan('update:channel', ['channel_id' => 'unknown_channel_id'])
            ->assertExitCode(1)
        ;
    }

    /** @test */
    public function real_channel_id_should_succeed(): void
    {
        // faking playlist items response
        $this->fakePlaylistItemsResponse($this->playlistId);

        // no medias before
        $this->assertCount(0, $this->channel->medias);

        // command should run properly
        $this->artisan('update:channel', ['channel_id' => $this->channel->youtube_id])
            ->assertExitCode(0)
        ;
        $this->channel->refresh();

        // channel should have medias (from tests/Fixtures/Youtube/playlistItems-response.json)
        $expectedMedias = [
            'EePwbhMqEh0' => 'FAKED - 2015 10 20 Natacha Christian versus Nolwen Fred 01',
            '9pTBAkkTRbw' => 'FAKED - 20120604-match-Christian-RomainC-VS-Ludo-Fred',
        ];
        $this->assertCount(count($expectedMedias), $this->channel->medias);

        $this->assertEqualsCanonicalizing(
            $expectedMedias,
            $this->channel->medias->pluck('title', 'media_id')->toArray()
        );
    }
}
