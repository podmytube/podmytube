<?php

namespace Tests\Unit;

use App\Thumb;
use App\Channel;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThumbModelTest extends TestCase
{
  //use RefreshDatabase;

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
    self::$thumb = factory(Thumb::class)->create([
      'channel_id' => self::$channel->channelId(),
    ]);
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
    $this->assertEquals($expectedUrl, Thumb::defaultUrl());
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
    $expectedResult =
      self::$thumb->channelId() . '/' . self::$thumb->fileName();
    $this->assertEquals($expectedResult, self::$thumb->relativePath());
  }

  /**
   * @depends testingRelativePath
   */
  public function testingDashboardUrl()
  {
    $expectedUrl =
      env('APP_URL') . "/storage/thumbs/" . self::$thumb->relativePath();
    $this->assertEquals($expectedUrl, self::$thumb->dashboardUrl());
  }

  /**
   * @depends testingDashboardUrl
   */
  public function testingPodcastUrl()
  {
    $expectedUrl = env('THUMBS_URL') . '/' . self::$thumb->relativePath();
    $this->assertEquals($expectedUrl, self::$thumb->podcastUrl());
  }

  /**
   * @depends testingPodcastUrl
   */
  public function testingChannelReplaceItsThumb()
  {
    /** creating fake uploaded image */
    $uploadedFile = UploadedFile::fake()->image(
      '/tmp/fakeThumbThatShouldNeverExist.jpg',
      '1400',
      '1400'
    );

    /** attach it to channel */
    $this->assertInstanceOf(
      Thumb::class,
      Thumb::make()->attachItToChannel($uploadedFile, self::$channel)
    );
  }

  public function testingChannelGetItsFirstThumb()
  {
    /** creating fake uploaded image */
    $uploadedFile = UploadedFile::fake()->image(
      '/tmp/fakeThumbThatShouldNeverExist.jpg',
      '1400',
      '1400'
    );

    /** creating new channel */
    $channel = factory(Channel::class)->create();

    /** attach it to channel */
    $this->assertInstanceOf(
      Thumb::class,
      Thumb::make()->attachItToChannel($uploadedFile, $channel)
    );
  }

  public function testingThumbDoesNotExist()
  {
    $thumb = new Thumb();
    $this->assertFalse($thumb->exists());
  }
}
