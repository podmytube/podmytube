<?php

namespace Tests\Unit;

use App\Exceptions\DownloadMediaFailureException;
use App\Media;
use App\Modules\DownloadYTMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class DownloadYTMediaTest extends TestCase
{
    use RefreshDatabase;

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

    /** @var \App\Media $media */
    protected $media;

    /**
     * first things to do before launching tests
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->media = factory(Media::class)->create(['media_id' => self::MARIO_COIN_VIDEO]);
        $this->destinationFolder = '/tmp/';
        $this->expectedVideoFile = $this->destinationFolder . self::MARIO_COIN_VIDEO . self::AUDIO_FILE_EXTENSION;
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
            DownloadYTMedia::init($this->media, $this->destinationFolder, false)->downloadedFilePath(),
            'expected file {' . $this->expectedVideoFile . '} should be there'
        );
    }

    /**
     * This will test if download is working well.
     * @return void
     */
    public function testDownloadShouldBeGood()
    {
        DownloadYTMedia::init($this->media, $this->destinationFolder, false)->download();
        $this->assertFileExists(
            $this->expectedVideoFile,
            'expected file {' . $this->expectedVideoFile . '} should be there'
        );
    }

    public function testExistingFileShouldBeRemovedBeforeDownload()
    {
        $expectedFileToBeRemovedBefore = $this->destinationFolder . '/' . self::MARIO_COIN_VIDEO . '.mp4';
        /** creating fake file */
        touch($expectedFileToBeRemovedBefore);
        $this->assertFileExists($expectedFileToBeRemovedBefore);
        DownloadYTMedia::init($this->media, $this->destinationFolder, false);
        $this->assertFileDoesNotExist(
            $expectedFileToBeRemovedBefore,
            'Mp4 file should have been revoed first, before youtube-dl'
        );
    }

    /**
     * This will test that we throw an exception destination folder is not valid (writable and exists)
     */
    public function testThatWeFailIfDestinationPathIsInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        DownloadYTMedia::init($this->media, '/path/that/does/not/exists');
    }

    /**
     * This will test that we throw an exception if mediaid is not valid
     */
    public function testThatWeFailIfMediaIsInvalid()
    {
        $foolishMedia = factory(Media::class)->create(['media_id' => 'invalid-media-forever']);
        $this->expectException(DownloadMediaFailureException::class);
        DownloadYTMedia::init($foolishMedia, $this->destinationFolder)->download();
    }
}
