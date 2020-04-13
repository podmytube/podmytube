<?php

namespace Tests\Unit;

use App\Channel;
use App\Thumb;
use App\Modules\Vignette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VignetteModuleTest extends TestCase
{
    /** used to remove every created data in database */
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
        self::$channel = factory(Channel::class)->create();
        self::$thumb = factory(Thumb::class)->create([
            'channel_id' => self::$channel->channel_id,
        ]);
        self::$dbIsWarm = true;
    }

    public static function tearDownAfterClass(): void
    {
        /** removing local thumb img */
        Storage::disk(self::$thumb->fileDisk())->deleteDirectory(
            self::$thumb->channelId()
        );
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
        $expectedUrl =
            env('THUMBS_URL') . '/' . Vignette::DEFAULT_VIGNETTE_FILE;
        $this->assertEquals($expectedUrl, Vignette::defaultUrl());
    }

    public function testingFileName()
    {
        $vigObj = Vignette::fromThumb(self::$thumb);

        list($fileName, $fileExtension) = explode(
            '.',
            self::$thumb->fileName()
        );
        $expectedFileName =
            $fileName . Vignette::VIGNETTE_SUFFIX . '.' . $fileExtension;
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
        $expectedRelativePath =
            self::$channel->channel_id .
            '/' .
            $fileName .
            Vignette::VIGNETTE_SUFFIX .
            '.' .
            $fileExtension;
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
        $this->assertInstanceOf(Vignette::class, $vigObj->makeIt());
        return $vigObj;
    }

    /**
     * @depends testingMakingVignetteFromThumb
     */
    public function testingFinallyVignetteShouldExists($vigObj)
    {
        $this->assertTrue($vigObj->exists());
        return $vigObj;
    }

    /**
     * @depends testingFinallyVignetteShouldExists
     */
    public function testingUploadVignetteIsRunningFine($vigObj)
    {
        $this->assertTrue($vigObj->upload());
        return $vigObj;
    }

    /**
     * @depends testingUploadVignetteIsRunningFine
     */
    public function testingRemovingLocalVignette($vigObj)
    {
        /** removing local vig img */
        /** removing remote vig img */
        $this->assertTrue($vigObj->delete());
        $this->assertFalse($vigObj->exists());
    }
}
