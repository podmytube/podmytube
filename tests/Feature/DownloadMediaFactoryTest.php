<?php

namespace Tests\Feature;

use App\Channel;
use App\Exceptions\DownloadMediaTagException;
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
        $this->expectException(YoutubeMediaDoesNotExistException::class);
        DownloadMediaFactory::media($media)->run();
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
        $this->expectException(DownloadMediaTagException::class);
        DownloadMediaFactory::media($media)->run();
    }

    public function testVideoIsBeingDownloaded()
    {
        $expectedMediaLength = 26898;
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
        $this->assertEquals($expectedMediaLength, $media->length);
        $this->assertEquals($expectedDuration, $media->duration);
        Bus::assertDispatched(SendFileBySFTP::class);
    }
}
