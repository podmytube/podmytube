<?php

namespace Tests\Unit;

use App\Channel;
use App\Thumb;
use App\Modules\Vignette;
use Tests\TestCase;

class VignetteModuleTest extends TestCase
{
    protected static $dbIsWarm = false;
    protected static $channel;
    protected static $thumb;

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

    public function testingFileName()
    {
        $vigObj = Vignette::fromThumb(self::$thumb);

        list($fileName, $fileExtension) = explode('.', self::$thumb->fileName());
        $expectedFileName = $fileName . Vignette::_VIGNETTE_SUFFIX . '.' . $fileExtension;
        $this->assertEquals($expectedFileName, $vigObj->fileName());
        return $vigObj;
    }

    /**
     * @depends testingFileName
     */
    public function testingChannelPath($vigObj)
    {
        $expectedChannelPath = self::$channel->channel_id . '/';
        $this->assertEquals($expectedChannelPath, $vigObj->channelPath());
        return $vigObj;
    }

    /**
     * @depends testingChannelPath
     */
    public function testingVignetteRelativePath($vigObj)
    {
        list($fileName, $fileExtension) = explode('.', self::$thumb->fileName());
        $expectedRelativePath = self::$channel->channel_id . '/' . $fileName . Vignette::_VIGNETTE_SUFFIX . '.' . $fileExtension;
        $this->assertEquals($expectedRelativePath, $vigObj->relativePath());
        return $vigObj;
    }

    /**
     * @depends testingVignetteRelativePath
     */
    public function testingVignetteFileShouldNotExists($vigObj)
    {
        $this->assertFalse($vigObj->exists());
        return $vigObj;
    }

    /**
     * @depends testingVignetteFileShouldNotExists
     */
    public function testingVignetteDiskShouldBeEqualToThumbDisk($vigObj)
    {
        $this->assertEquals(self::$thumb->fileDisk(), $vigObj->fileDisk());
        return $vigObj;
    }

    /**
     * @depends testingVignetteDiskShouldBeEqualToThumbDisk
     */
    public function testingMakingVignetteFromThumb($vigObj)
    {
        $this->assertTrue($vigObj->make());
        $this->assertTrue($vigObj->exists());
    }

    public function testingThatThumbExistsFails()
    {
        $this->expectException(Exception::class);
        $channel = factory(Channel::class)->make();
        $thumb = factory(Thumb::class)->create(['channel_id' => $channel]);        
        $vigObj = Vignette::fromThumb($thumb);
    }
}
