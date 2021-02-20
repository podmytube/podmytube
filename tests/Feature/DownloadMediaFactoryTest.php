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

    /** this video does exist and has two tags ['dev', 'podmytube'] */
    protected const BEACH_VOLLEY_VIDEO_1 = 'EePwbhMqEh0';
    protected const BEACH_VOLLEY_VIDEO_2 = '9pTBAkkTRbw';
    protected const MARIO_COIN_VIDEO = 'qfx6yf8pux4';

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
        //$channel = factory(Channel::class)->create(['accept_video_by_tag' => 'window,house']);
        $this->channel->accept_video_by_tag = 'window,house';
        $this->channel->save();

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
        $this->assertEquals(26898, $media->length);
        $this->assertEquals(5, $media->duration);
        Bus::assertDispatched(SendFileBySFTP::class);
    }
}
