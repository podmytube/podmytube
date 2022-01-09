<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use App\Jobs\ChannelHasReachedItsLimitsJob;
use App\Media;
use App\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use RuntimeException;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UpdateChannelsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->seedPlans();
        $this->starterPlan = Plan::bySlug('starter');
    }

    /** @test */
    public function update_channels_with_no_active_channel_should_throw_exception(): void
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('update:channels')->assertExitCode(1);
    }

    /** @test */
    public function update_channels_should_add_new_medias_only_on_active_channels(): void
    {
        $expectedNumberOfMedias = 2;
        $channel = $this->createMyOwnChannel($this->starterPlan);
        $inactiveChannel = $this->createChannel(null, $this->starterPlan);
        $inactiveChannel->update(['active' => false]);

        $this->assertCount(0, Media::all());
        $this->artisan('update:channels')->assertExitCode(0);
        $this->assertCount($expectedNumberOfMedias, Media::all());
        $this->assertGreaterThan(0, $channel->medias->count());
        $this->assertCount(0, $inactiveChannel->medias);
    }

    /** @test */
    public function update_channels_should_update_medias_successfully(): void
    {
        $expectedNumberOfMedias = 2;
        $this->createMyOwnChannel($this->starterPlan);
        $this->assertCount(0, Media::all());
        $this->artisan('update:channels')->assertExitCode(0);
        $this->assertCount($expectedNumberOfMedias, Media::all());
        $this->artisan('update:channels')->assertExitCode(0);
    }

    /** @test */
    public function update_channels_should_update_deleted_medias_successfully(): void
    {
        $mediaId = 'EePwbhMqEh0';
        $expectedNumberOfMedias = 2;
        $channel = $this->createMyOwnChannel($this->starterPlan);
        factory(Media::class)->create([
            'media_id' => $mediaId,
            'channel_id' => $channel->channelId(),
            'title' => 'foo',
            'deleted_at' => now(),
        ]);
        $this->assertCount(1, Media::withTrashed()->get());
        $this->artisan('update:channels')->assertExitCode(0);
        $this->assertCount($expectedNumberOfMedias, Media::withTrashed()->get());
        $this->artisan('update:channels')->assertExitCode(0);

        // checking media as really been updated
        $media = Media::byMediaId($mediaId, true);
        $this->assertEquals('2015 10 20 Natacha Christian versus Nolwen Fred 01', $media->title);
        $this->assertEquals('2015-10-28', $media->publishedAt());
    }

    /** @test */
    public function update_channels_should_warn_when_exceeded_quota(): void
    {
        Bus::fake();
        // creating my own channel
        $channel = $this->createMyOwnChannel($this->starterPlan);
        // adding grabbed medias more than my plan should permit
        $this->addGrabbedMediasToChannel($channel, 10);
        // running update should add 2 medias and warn me
        $this->artisan('update:channels')->assertExitCode(0);
        Bus::assertDispatched(ChannelHasReachedItsLimitsJob::class);
    }
}
