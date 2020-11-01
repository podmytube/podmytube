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
    /**
     * Youtube media id for mario coin sound effect.
     */
    const mediaToObtain = 'qfx6yf8pux4';

    /**
     * default video extension
     */
    const mediaExt = '.mp3';

    /**
     * Where the video should be stored
     */
    const mediaDestinationFolder = '/tmp';

    /**
     * Mario sound effect is 0:06 seconds long
     */
    const mediaDuration = 6;

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

    /**
     * first things to do before launching tests
     * @return void
     */
    protected function setUp(): void
    {
        $this->expectedVideoFile = self::mediaDestinationFolder . DIRECTORY_SEPARATOR . self::mediaToObtain . self::mediaExt;
        if (file_exists($this->expectedVideoFile)) {
            unlink($this->expectedVideoFile);
        }
    }

    /**
     * This will test if download is working well.
     * @return void
     */
    public function testDownloadShouldBeGood()
    {
        DownloadYTMedia::init(self::mediaToObtain, self::mediaDestinationFolder, false)->download();
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
        DownloadYTMedia::init(self::mediaToObtain, '/path/that/does/not/exists');
    }

    /**
     * This will test that we throw an exception if mediaid is not valid
     */
    public function testThatWeFailIfMediaIsInvalid()
    {
        $this->expectException(DownloadMediaFailureException::class);
        DownloadYTMedia::init('invalid-media-forever', '/tmp')->download();
    }
}
