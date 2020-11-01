<?php

namespace Tests\Feature;

use App\Channel;
use App\Exceptions\YoutubeMediaDoesNotExistException;
use App\Factories\DownloadMediaFactory;
use App\Media;
use Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DownloadMediaFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** this video does exist and has two tags ['dev', 'podmytube'] */
    protected const BEACH_VOLLEY_VIDEO = 'EePwbhMqEh0';
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

    public function testVideoDoesNotHaveTheGoodTags()
    {
        $channel = factory(Channel::class)->create(['accept_video_by_tag' => 'window,house']);
        $media = factory(Media::class)->create(
            [
                'channel_id' => $channel->channel_id,
                'media_id' => self::BEACH_VOLLEY_VIDEO
            ]
        );
        $this->assertFalse(
            DownloadMediaFactory::media($media)->run(),
            'channel only want window and house videos, dev/podmytube are not accepted'
        );
    }

    public function testVideoIsBeingDownloaded()
    {
        $media = factory(Media::class)->create(
            [
                'media_id' => self::MARIO_COIN_VIDEO
            ]
        );
        $this->assertTrue(
            DownloadMediaFactory::media($media)->run(),
            'channel wants dev or podmytube tags, video should have been downloaded  not accepted'
        );
    }
}
