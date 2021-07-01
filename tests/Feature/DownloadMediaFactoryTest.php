<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use App\Factories\DownloadMediaFactory;
use App\Jobs\SendFileBySFTP;
use App\Media;
use App\Plan;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Storage;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DownloadMediaFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel */
    protected $channel;

    /** @var \App\Media */
    protected $media;

    /** \App\Subscription $subscription */
    protected $subscription;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'PlansTableSeeder']);
        Bus::fake(SendFileBySFTP::class);
        $this->channel = factory(Channel::class)->create(['channel_id' => 'test']);
        $this->subscription = factory(Subscription::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'plan_id' => Plan::bySlug('forever_free')->id,
            ]
        );

        $marioCoinDownloadedFilePath = Storage::disk('tmp')->path(self::MARIO_COIN_VIDEO.'.mp3');
        if (file_exists($marioCoinDownloadedFilePath)) {
            unlink($marioCoinDownloadedFilePath);
        }
    }

    /** YT media does not exists - should throw exception */
    public function test_invalid_media_should_be_rejected(): void
    {
        $this->media = factory(Media::class)->create([
            'channel_id' => $this->channel->channel_id,
            'media_id' => 'absolutely-not-valid',
            'title' => null,
            'description' => null,
        ]);
        DownloadMediaFactory::media($this->media)->run();
        $this->assertEquals(Media::STATUS_NOT_AVAILABLE_ON_YOUTUBE, $this->media->status);
        $this->assertNull($this->media->title);
        $this->assertNull($this->media->description);

        $this->someChecksWhenNotDowloaded();
    }

    /** @test */
    public function episode_should_not_being_grabbed_because_too_old(): void
    {
        // channel accept only episode from now
        $this->channel->update(['reject_video_too_old' => now()]);

        $this->media = factory(Media::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::BEACH_VOLLEY_VIDEO_1,
            ]
        );
        DownloadMediaFactory::media($this->media)->run();
        $this->assertEquals(Media::STATUS_AGE_FILTERED, $this->media->status);

        $this->someChecksWhenNotDowloaded();
    }

    public function test_video_is_being_downloaded(): void
    {
        $expectedMediaLength = [26666, 26898];
        $expectedDuration = 5;
        $this->media = factory(Media::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::MARIO_COIN_VIDEO,
            ]
        );
        $this->assertTrue(DownloadMediaFactory::media($this->media)->run(), 'channel video should have been processed');
        $this->media = Media::byMediaId(self::MARIO_COIN_VIDEO);
        $this->assertNotNull($this->media);
        $this->assertEquals('Super Mario Bros. - Coin Sound Effect', $this->media->title);
        // same video is giving me 2 length 26898 && 26666 depends of lib/python installed
        $this->assertTrue(in_array($this->media->length, $expectedMediaLength));
        $this->assertEquals($expectedDuration, $this->media->duration);
        $this->assertEquals(Media::STATUS_DOWNLOADED, $this->media->status);
        $this->assertEquals(5, $this->media->duration);
        Bus::assertDispatched(SendFileBySFTP::class);
    }

    public function someChecksWhenNotDowloaded(): void
    {
        $this->assertNull($this->media->grabbed_at);
        $this->assertEquals(0, $this->media->length);
        $this->assertEquals(0, $this->media->duration);
    }

    /** @test */
    public function channel_has_reached_its_limit(): void
    {
        // adding grabbed media(s) to channel (with free plan)
        factory(Media::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::MARIO_COIN_VIDEO,
                'grabbed_at' => now(),
            ]
        );

        // channel has a media to download
        $this->media = factory(Media::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::MARIO_MUSHROOM_VIDEO,
                'grabbed_at' => null,
            ]
        );

        DownloadMediaFactory::media($this->media)->run();
        $this->assertEquals(Media::STATUS_EXHAUSTED_QUOTA, $this->media->status);
    }
}
