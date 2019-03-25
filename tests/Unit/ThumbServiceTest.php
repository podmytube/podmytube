<?php

namespace Tests\Unit;

use App\Channel;
use App\Services\ThumbService;
use Tests\TestCase;

class ThumbServiceTest extends TestCase
{
    protected $expectedThumbUrl = null;
    protected $expectedVigUrl = null;
    protected const STORAGE_THUMBS_PATH = "/storage/thumbs/";

    protected const VALID_CHANNEL = "earlyChannel";
    protected const VALID_SAMPLE_FILE = "sampleThumb.jpg";

    public function setUp()
    {
        parent::setUp();
        $this->expectedThumbUrl = env('APP_URL') . self::STORAGE_THUMBS_PATH . ThumbService::DEFAULT_THUMB_FILE;
        $this->expectedVigUrl = env('APP_URL') . self::STORAGE_THUMBS_PATH . ThumbService::DEFAULT_VIGNETTE_FILE;
    }

    /**
     * This channel has one thumb in db but file is not present => default thumb should be returned
     */
    public function testEarlyChannelHasEverythingOk()
    {
        $channel = Channel::find(self::VALID_CHANNEL);
        $expected = env('APP_URL') . self::STORAGE_THUMBS_PATH . $channel->channel_id . '/' . self::VALID_SAMPLE_FILE;
        $result = ThumbService::getChannelThumbUrl($channel);
        $this->assertEquals(
            $expected,
            $result,
            "Channel {{$channel->channel_id}} should have its real thumb {{$expected}} and result was {{$result}}"
        );
    }

    /**
     * This channel has one thumb in db but file is not present => default thumb should be returned
     */
    public function testFreeChannelHasAThumbInDBButNoFileIsPresent()
    {
        $channel = Channel::find("freeChannel");
        $result = ThumbService::getChannelThumbUrl($channel);
        $this->assertEquals(
            $this->expectedThumbUrl,
            $result,
            "Channel {{$channel->channel_id}} should have default thumb {{$this->expectedThumbUrl}} and result was {{$result}}"
        );
    }

    /**
     * This channel has no thumb associated => default thumb should be returned
     */
    public function testInvalidChannelShouldHaveDefaultThumb()
    {
        $channel = Channel::find("invalidChannel");
        $result = ThumbService::getChannelThumbUrl($channel);
        $this->assertEquals(
            $this->expectedThumbUrl,
            $result,
            "Channel {{$channel->channel_id}} should have default thumb {{$this->expectedThumbUrl}} and result was {{$result}}"
        );
    }
}
