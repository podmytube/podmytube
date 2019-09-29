<?php

namespace Tests\Unit;

use App\Channel;
use App\Services\ThumbService;
use App\Thumb;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ThumbServiceTest extends TestCase
{
    /**
     * Tell id DB has been seed with some item first.
     * @var Boolean $initDone 
     */
    protected static $initDone = false;

    /**
     * Thumb provided by the factory creation
     * @var Thumb $thumb
     */
    protected static $thumb;


    /**
     * ------------------------------------------------------------------------
     */
    public function testingChannelWithNoThumbShouldGetDefaultUrl()
    {
        /**
         * Creating one channel for this test
         */
        $channel = factory(Channel::class)->create();
        $this->assertEquals(
            ThumbService::getDefaultThumbUrl(),
            ThumbService::getChannelThumbUrl($channel)
        );
        return $channel;
    }

    /**
     * @depends testingChannelWithNoThumbShouldGetDefaultUrl
     */
    public function testingAddingThumbToChannel($channel)
    {
        $fileName = 'fakeThumbThatShouldNeverExist.jpg';
        /**
         * File should not be present at this time
         */
        if (Storage::disk(Thumb::_STORAGE_DISK)->exists($channel->channel_id . DIRECTORY_SEPARATOR . $fileName)) {
            Storage::disk(Thumb::_STORAGE_DISK)->delete($channel->channel_id . DIRECTORY_SEPARATOR . $fileName);
        }

        $uploadedFile = UploadedFile::fake()->image($fileName, '1400', '1400');
        ThumbService::create()->addUploadedThumb($uploadedFile, $channel);
        $this->markTestIncomplete( 'This test has not been implemented yet.' );
    }
    /**
     * ========================================================================
     */

    public function testingGetDefaultThumbUrl()
    {
        $this->assertEquals(
            getenv('THUMBS_URL') . '/' . Thumb::_DEFAULT_THUMB_FILE,
            ThumbService::getDefaultThumbUrl()
        );
    }

    public function testingGetDefaultVignetteUrl()
    {
        $this->assertEquals(
            getenv('THUMBS_URL') . '/' . Thumb::_DEFAULT_VIGNETTE_FILE,
            ThumbService::getDefaultVignetteUrl()
        );
    }

    public function testingThatDefaultFilesArePresent()
    {
        foreach ([
            Thumb::_DEFAULT_THUMB_FILE,
            Thumb::_DEFAULT_VIGNETTE_FILE,
        ] as $defaultFile) {
            $this->assertTrue(
                ThumbService::pathExists($defaultFile),
                "Default thumb file {$defaultFile} is missing on {" . Thumb::_STORAGE_DISK . "}."
            );
        }
    }

    public function testingInvalidThumbFileShouldBeMissing()
    {
        $this->assertFalse(ThumbService::pathExists("/this/file/will/never/exist"));
    }

    /**
     * This function will create one thumb (and user+channel).
     * I'm not using setUpBeforeClass because it doesn't work out
     * of the bow with laravel and this way is pretty clear and simple.
     */
    protected static function initDB(): void
    {
        self::$thumb = factory(Thumb::class)->create();
        self::$initDone = true;
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$initDone) {
            self::initDB();
        }
    }
}
