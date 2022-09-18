<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\ChannelHasReachedItsLimitsJob;
use App\Models\Media;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use RuntimeException;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UpdateChannelCommandTest extends TestCase
{
    use RefreshDatabase;

    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->starterPlan = Plan::factory()->name('starter')->create();
    }

    /** @test */
    public function update_channel_with_no_channel_should_fail(): void
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('update:channel')->assertExitCode(1);
    }

    /** @test */
    public function update_channel_should_fail_on_invalid_channel(): void
    {
        $this->artisan('update:channel', ['channel_id' => 'invalid-channel-id'])->assertExitCode(1);
    }

    /** @test */
    public function update_channel_should_succeed_to_add_new_medias(): void
    {
        $expectedNumberOfMedias = 2;
        $channel = $this->createMyOwnChannel($this->starterPlan);
        $this->assertCount(0, $channel->medias);
        $this->artisan('update:channel', ['channel_id' => $channel->channel_id])
            ->assertExitCode(0)
        ;
        $channel->refresh();
        $this->assertCount($expectedNumberOfMedias, $channel->medias);
    }

    /** @test */
    public function update_channel_should_succeed_to_update_deleted_media(): void
    {
        $mediaId = 'EePwbhMqEh0';
        $channel = $this->createMyOwnChannel($this->starterPlan);
        Media::factory()->create([
            'media_id' => $mediaId,
            'channel_id' => $channel->channelId(),
            'title' => 'foo',
            'deleted_at' => now(),
        ]);
        $this->assertCount(0, $channel->medias);
        $this->artisan('update:channel', ['channel_id' => $channel->channel_id])
            ->assertExitCode(0)
        ;
        $this->assertCount(0, $channel->medias);

        // checking media as really been updated
        $media = Media::byMediaId($mediaId, true);
        $this->assertEquals('2015 10 20 Natacha Christian versus Nolwen Fred 01', $media->title);
        $this->assertEquals('2015-10-28', $media->publishedAt());
    }

    /** @test */
    public function update_channel_should_succeed_to_update_existing_medias(): void
    {
        $expectedNumberOfMedias = 2;
        $channel = $this->createMyOwnChannel($this->starterPlan);
        $this->assertCount(0, $channel->medias);
        $this->artisan('update:channel', ['channel_id' => $channel->channel_id])
            ->assertExitCode(0)
        ;
        $channel->refresh();
        $this->assertCount($expectedNumberOfMedias, $channel->medias);

        $this->artisan('update:channel', ['channel_id' => $channel->channel_id])
            ->assertExitCode(0)
        ;
        $channel->refresh();
        $this->assertCount($expectedNumberOfMedias, $channel->medias);
    }

    /** @test */
    public function update_channel_should_warn_when_exceeded_quota(): void
    {
        Bus::fake();
        // creating my own channel
        $channel = $this->createMyOwnChannel($this->starterPlan);
        // adding grabbed medias more than my plan should permit
        $this->addGrabbedMediasToChannel($channel, 10);
        // running update should add 2 medias and warn me
        $this->artisan('update:channel', ['channel_id' => $channel->channel_id])
            ->assertExitCode(0)
        ;
        Bus::assertDispatched(ChannelHasReachedItsLimitsJob::class);
    }
}
