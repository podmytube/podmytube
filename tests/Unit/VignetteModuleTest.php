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
    public function testingVignetteRelativePath($vigObj)
    {
        $fileName = pathinfo(self::$thumb->fileName(), PATHINFO_FILENAME);
        $fileExtension = pathinfo(self::$thumb->fileName(), PATHINFO_EXTENSION);
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
    public function testingMakingVignetteFromThumb($vigObj)
    {
        $this->assertTrue($vigObj->make());
        return $vigObj;
    }

    /**
     * @depends testingMakingVignetteFromThumb
     */
    public function testingFinallyVignetteShouldExists($vigObj)
    {
        $this->assertTrue($vigObj->exists());
    }
}
