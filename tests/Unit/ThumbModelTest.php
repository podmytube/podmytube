<?php

namespace Tests\Unit;

use App\Thumb;
use App\Channel;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThumbModelTest extends TestCase
{
    use RefreshDatabase;
    
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
        /* if (env('APP_ENV') == 'testing') {
            DB::table('channels')->delete();
        } */
        self::$channel = factory(Channel::class)->create();
        self::$thumb = factory(Thumb::class)->create(['channel_id' => self::$channel->channelId()]);
        self::$dbIsWarm = true;
    }

    public function setUp(): void
    {
        parent::setUp();
        if (!static::$dbIsWarm) {
            static::warmDb();
        }
    }

    public static function tearDownAfterClass(): void
    {
        /**
         * Laravel app is destroyed on tearDown method. 
         * TearDownAfterClass come after, so nothing is working.
         * - self::$channel->delete => KO
         * - query builder => KO
         * - $this->beforeApplicationDestroyed(function () {
         *       //self::$channel->delete();
         *   }); => KO 
         */
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

    public function testingFileName()
    {
        $this->assertEquals(self::$thumb->file_name, self::$thumb->fileName());
    }

    public function testingChannelId()
    {
        $this->assertEquals(self::$channel->channelId(), self::$thumb->channelId());
    }

    /**
     * @depends testingChannelId
     */
    public function testingRelativePath()
    {
        $expectedResult = self::$thumb->channelId() . '/' . self::$thumb->fileName();
        $this->assertEquals($expectedResult, self::$thumb->relativePath());
    }

    /**
     * @depends testingRelativePath
     */
    public function testingDashboardUrl()
    {
        $expectedUrl = env('APP_URL') . "/storage/thumbs/" . self::$thumb->relativePath();
        $this->assertEquals($expectedUrl, self::$thumb->dashboardUrl());
    }

    /**
     * @depends testingRelativePath
     */
    public function testingPodcastUrl()
    {
        $expectedUrl = env('THUMBS_URL') . '/' . self::$thumb->relativePath();
        $this->assertEquals($expectedUrl, self::$thumb->podcastUrl());
    }

    public function testingfromUploadedFile()
    {
        /** creating fake uploaded image */
        $uploadedFile = UploadedFile::fake()->image('/tmp/fakeThumbThatShouldNeverExist.jpg', '1400', '1400');

        /** attach it to channel */
        $thumb = Thumb::make()->attachItToChannel($uploadedFile, self::$channel);

        /** checking */
        $this->assertInstanceOf(Thumb::class, $thumb);
        $this->assertEquals($thumb->channelId(), self::$channel->channelId());
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
