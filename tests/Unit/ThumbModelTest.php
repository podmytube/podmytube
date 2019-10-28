<?php

namespace Tests\Unit;

use App\Channel;
use App\Thumb;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ThumbModelTest extends TestCase
{
    /** used to remove every created data in database */
    use DatabaseTransactions;

    /** @var bool true, database is ready to run tests upon */
    protected static $dbIsWarm = false;

    /** @var Channel channel obj used for the test */
    protected static $channel;

    /** @var Thumb thumb object used by the tests */
    protected static $thumb;

    /**
     * This function will create a channel, thumb and everyt item required to run theses tests.
     */
    protected static function warmDb()
    {
        self::$channel = factory(Channel::class)->create();
        self::$thumb = factory(Thumb::class)->create(['channel_id' => self::$channel->channel_id]);
        self::$dbIsWarm = true;
    }

    public function setUp(): void
    {
        parent::setUp();
        if (!static::$dbIsWarm) {
            static::warmDb();
        }
    }

    public function testingDefaultUrl()
    {
        $expectedUrl = env('THUMBS_URL') . '/' . Thumb::_DEFAULT_THUMB_FILE;
        $this->assertEquals(
            $expectedUrl,
            Thumb::defaultUrl()
        );
    }

    public function testingThumbExists()
    {
        $this->assertTrue(self::$thumb->exists());
    }

    public function testingFileName ()
    {
        $this->assertEquals(
            self::$thumb->file_name,
            self::$thumb->fileName()
        );
    }

    public function testingChannelId ()
    {
        $this->assertEquals(
            self::$channel->channel_id,
            self::$thumb->channelId()
        );
    }
    
    /**
     * @depends testingChannelId
     */
    public function testingRelativePath ()
    {
        $expectedResult = self::$thumb->channelId() . '/' . self::$thumb->fileName();
        $this->assertEquals(
            $expectedResult,
            self::$thumb->relativePath()
        );
    }

    /**
     * @depends testingRelativePath
     */
    public function testingDashboardUrl()
    {
        $expectedUrl = env('APP_URL') . "/storage/thumbs/" . self::$thumb->relativePath();
        $this->assertEquals(
            $expectedUrl,
            self::$thumb->dashboardUrl()
        );
    }

    /**
     * @depends testingRelativePath
     */
    public function testingPodcastUrl()
    {
        $expectedUrl = env('THUMBS_URL') . '/' . self::$thumb->relativePath();
        $this->assertEquals(
            $expectedUrl,
            self::$thumb->podcastUrl()
        );
    }


    /* public function testingUploadThumbIsRunningFine()
    {
        $this->assertTrue(self::$thumb->upload());
        echo self::$channel->channel_id;
    } */

    public function testingThumbDoesNotExist()
    {
        $thumb = new Thumb();
        $this->assertFalse($thumb->exists());
    }
}
