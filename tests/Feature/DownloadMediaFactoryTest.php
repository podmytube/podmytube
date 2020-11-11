<?php

namespace Tests\Feature;

use App\Channel;
use App\Exceptions\ChannelHasReachedItsQuotaException;
use App\Exceptions\DownloadMediaTagException;
use App\Exceptions\YoutubeMediaDoesNotExistException;
use App\Factories\DownloadMediaFactory;
use App\Media;
use App\Plan;
use App\Subscription;
use Artisan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DownloadMediaFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** this video does exist and has two tags ['dev', 'podmytube'] */
    protected const BEACH_VOLLEY_VIDEO_1 = 'EePwbhMqEh0';
    protected const BEACH_VOLLEY_VIDEO_2 = '9pTBAkkTRbw';
    protected const MARIO_COIN_VIDEO = 'qfx6yf8pux4';

    public function setUp():void
    {
        parent::setUp();
        Artisan::call('db:seed');
    }

    public function testInvalidMediaShouldBeRejected()
    {
        $media = factory(Media::class)->create(['media_id' => 'absolutely-not-valid']);
        $this->expectException(YoutubeMediaDoesNotExistException::class);
        DownloadMediaFactory::media($media)->run();
    }

    public function testVideoHasATagProblem()
    {
        $channel = factory(Channel::class)->create(['accept_video_by_tag' => 'window,house']);
        $media = factory(Media::class)->create(
            [
                'channel_id' => $channel->channel_id,
                'media_id' => self::BEACH_VOLLEY_VIDEO_1
            ]
        );
        $this->expectException(DownloadMediaTagException::class);
        DownloadMediaFactory::media($media)->run();
    }

    public function testFreeChannelHasReachedItsQuota()
    {
        $channel = factory(Channel::class)->create();
        factory(Subscription::class)->create(
            [
                'channel_id' => $channel->channel_id,
                'plan_id' => Plan::bySlug('forever_free')->id
            ]
        );
        factory(Media::class)->create(
            [
                'channel_id' => $channel->channel_id,
                'media_id' => self::BEACH_VOLLEY_VIDEO_1,
                'grabbed_at' => Carbon::now(),
            ]
        );

        $media = factory(Media::class)->create(
            [
                'channel_id' => $channel->channel_id,
                'media_id' => self::BEACH_VOLLEY_VIDEO_2,
            ]
        );

        $this->expectException(ChannelHasReachedItsQuotaException::class);
        DownloadMediaFactory::media($media)->run();
    }

    public function testVideoIsBeingDownloaded()
    {
        $subscription = factory(Subscription::class)->create(
            [
                'plan_id' => Plan::bySlug('forever_free')->id
            ]
        );
        $media = factory(Media::class)->create(
            [
                'channel_id' => $subscription->channel_id,
                'media_id' => self::MARIO_COIN_VIDEO
            ]
        );
        $this->assertTrue(
            DownloadMediaFactory::media($media, true)->run(),
            'channel video should have been processed'
        );
        $media = Media::byMediaId(self::MARIO_COIN_VIDEO);
        $this->assertNotNull($media);
        $this->assertEquals('Super Mario Bros. - Coin Sound Effect', $media->title);
        $this->assertEquals(86469, $media->length);
        $this->assertEquals(5, $media->duration);
        $this->assertTrue($media->remoteFileExists());
    }
}
