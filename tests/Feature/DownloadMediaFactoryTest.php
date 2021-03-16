<?php

namespace Tests\Feature;

use App\Channel;
use App\Exceptions\YoutubeMediaDoesNotExistException;
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

class DownloadMediaFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** \App\Channel $channel */
    protected $channel;

    /** \App\Subscription $subscription */
    protected $subscription;

    public function setUp():void
    {
        parent::setUp();
        Artisan::call('db:seed');
        Bus::fake(SendFileBySFTP::class);
        $this->channel = factory(Channel::class)->create(['channel_id' => 'test']);
        $this->subscription = factory(Subscription::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'plan_id' => Plan::bySlug('forever_free')->id
            ]
        );

        $marioCoinDownloadedFilePath = Storage::disk('tmp')->path(self::MARIO_COIN_VIDEO . '.mp3');
        if (file_exists($marioCoinDownloadedFilePath)) {
            unlink($marioCoinDownloadedFilePath);
        }
    }

    /** YT media does not exists - should throw exception */
    public function testInvalidMediaShouldBeRejected()
    {
        $media = factory(Media::class)->create([
            'channel_id' => $this->channel->channel_id,
            'media_id' => 'absolutely-not-valid'
        ]);
        DownloadMediaFactory::media($media)->run();
        $this->assertEquals(Media::STATUS_NOT_AVAILABLE_ON_YOUTUBE, $media->status);
        $this->assertNull($media->title);
        $this->assertNull($media->description);
        $this->assertNull($media->grabbed_at);
        $this->assertEquals(0, $media->length);
        $this->assertEquals(0, $media->duration);
    }

    public function testVideoHasATagProblem()
    {
        /** channel accept only "window,mouse" tags - this video is tagged with "dev,podmytube"*/
        $this->channel->update(['accept_video_by_tag' => 'window,house']);

        $media = factory(Media::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::BEACH_VOLLEY_VIDEO_1
            ]
        );
        DownloadMediaFactory::media($media)->run();
        $this->assertEquals(Media::STATUS_TAG_FILTERED, $media->status);
        $this->assertNull($media->grabbed_at);
        $this->assertEquals(0, $media->length);
        $this->assertEquals(0, $media->duration);
    }

    public function testVideoIsBeingDownloaded()
    {
        $expectedMediaLength = [26666, 26898];
        $expectedDuration = 5;
        $media = factory(Media::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::MARIO_COIN_VIDEO
            ]
        );
        $this->assertTrue(DownloadMediaFactory::media($media)->run(), 'channel video should have been processed');
        $media = Media::byMediaId(self::MARIO_COIN_VIDEO);
        $this->assertNotNull($media);
        $this->assertEquals('Super Mario Bros. - Coin Sound Effect', $media->title);
        /** same video is giving me 2 length 26898 && 26666 depends of lib/python installed */
        $this->assertTrue(in_array($media->length, $expectedMediaLength));
        $this->assertEquals($expectedDuration, $media->duration);
        $this->assertEquals(Media::STATUS_DOWNLOADED, $media->status);
        $this->assertEquals(26666, $media->length);
        $this->assertEquals(5, $media->duration);
        Bus::assertDispatched(SendFileBySFTP::class);
    }
}
