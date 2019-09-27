<?php

namespace Tests\Unit;

use App\Channel;
use App\Services\ThumbService;
use App\Thumb;
use Tests\TestCase;

class ThumbServiceTest extends TestCase
{
    protected $expectedThumbUrl = null;
    protected $expectedVigUrl = null;
    protected const STORAGE_THUMBS_PATH = "/storage/thumbs/";

    protected const VALID_CHANNEL = "earlyChannel";
    protected const VALID_SAMPLE_THUMB_FILE = "sampleThumb.jpg";
    protected const VALID_SAMPLE_VIG_FILE = "sampleVig.jpg";

    /**
     * Tell id DB has been seed with some item first.
     * @var Boolean $initDone 
     */
    protected static $initDone = false;

    /**
     * This function will create one thumb (and user+channel).
     * I'm not using setUpBeforeClass because it doesn't work out
     * of the bow with laravel and this way is pretty clear and simple.
     */
    protected static function initDB(): void
    {
        factory(Thumb::class)->create();
        self::$initDone = true;
    }

    protected function setUp(): void
    {
        parent::setUp();

        /**
         * This should be thumb const properties.
         * @todo change that
         */
        $this->expectedThumbUrl = env('APP_URL') . self::STORAGE_THUMBS_PATH . ThumbService::DEFAULT_THUMB_FILE;
        $this->expectedVigUrl = env('APP_URL') . self::STORAGE_THUMBS_PATH . ThumbService::DEFAULT_VIGNETTE_FILE;

        if (!self::$initDone) {
            self::initDB();
        }
    }

    public function testfoo()
    {
        $this->assertFalse(false);
    }

    /*
    public function testCreateVigFromThumb()
    {
        $channel = Channel::find(self::VALID_CHANNEL);
        $result = ThumbService::createThumbVig($channel->thumb);
        $this->assertFileExists($result);
    }

    public function testEarlyChannelHasItsThumbOk()
    {
        $channel = Channel::find(self::VALID_CHANNEL);
        $expected = env('APP_URL') . self::STORAGE_THUMBS_PATH . $channel->channel_id . '/' . self::VALID_SAMPLE_THUMB_FILE;
        $result = ThumbService::getChannelThumbUrl($channel);
        $this->assertEquals(
            $expected,
            $result,
            "Channel {{$channel->channel_id}} should have its real thumb {{$expected}} and result was {{$result}}"
        );
    }

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
*/
}
