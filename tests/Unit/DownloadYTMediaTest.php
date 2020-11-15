<?php

/**
 * This class is the class test for the GetAudioTrackFromYTVideo class
 *
 * @category Test
 * @package  PodMyTube\core
 * @author   Frederick Tyteca <fred@podmytube.com>
 * @license  http://www.podmytube.com closed
 * @link     Podmytube website, http://www.podmytube.com
 */

use App\Exceptions\DownloadMediaFailureException;
use App\Modules\DownloadYTMedia;
use Tests\TestCase;

class DownloadYTMediaTest extends TestCase
{
    const MARIO_COIN_MEDIA_ID = 'qfx6yf8pux4';
    const AUDIO_FILE_EXTENSION = '.mp3';
    const MARIO_COIN_DURATION = 6;

    /**
     * Full path of the downloaded video
     */
    protected $expectedVideoFile;

    /**
     * The standard command line expected
     */
    protected $expectedCmdLineQuiet;

    /**
     * The verbose command line expected
     */
    protected $expectedCmdLineVerbose;

    /**
     * Instance of the object to test
     */
    protected $downloadVideo;

    /** @var string $destinationFolder */
    protected $destinationFolder;

    /**
     * first things to do before launching tests
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->expectedVideoFile = Storage::disk('tmp')->path(self::MARIO_COIN_MEDIA_ID . self::AUDIO_FILE_EXTENSION);
        $this->destinationFolder = Storage::disk('tmp')->path('');
        if (file_exists($this->expectedVideoFile)) {
            unlink($this->expectedVideoFile);
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->expectedVideoFile)) {
            unlink($this->expectedVideoFile);
        }
        parent::tearDown();
    }

    public function testDownloadedFilePathShouldBeGood()
    {
        $this->assertEquals(
            $this->expectedVideoFile,
            DownloadYTMedia::init(self::MARIO_COIN_MEDIA_ID, $this->destinationFolder, false)->downloadedFilePath(),
            'expected file {' . $this->expectedVideoFile . '} should be there'
        );
    }

    /**
     * This will test if download is working well.
     * @return void
     */
    public function testDownloadShouldBeGood()
    {
        DownloadYTMedia::init(self::MARIO_COIN_MEDIA_ID, $this->destinationFolder, false)->download();
        $this->assertFileExists(
            $this->expectedVideoFile,
            'expected file {' . $this->expectedVideoFile . '} should be there'
        );
    }

    /**
     * This will test that we throw an exception destination folder is not valid (writable and exists)
     */
    public function testThatWeFailIfDestinationPathIsInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        DownloadYTMedia::init(self::MARIO_COIN_MEDIA_ID, '/path/that/does/not/exists');
    }

    /**
     * This will test that we throw an exception if mediaid is not valid
     */
    public function testThatWeFailIfMediaIsInvalid()
    {
        $this->expectException(DownloadMediaFailureException::class);
        DownloadYTMedia::init('invalid-media-forever', $this->destinationFolder)->download();
    }
}
