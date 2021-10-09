<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\DownloadMediaFailureException;
use App\Media;
use App\Modules\DownloadYTMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DownloadYTMediaTest extends TestCase
{
    use RefreshDatabase;

    public const AUDIO_FILE_EXTENSION = '.mp3';
    public const MARIO_COIN_DURATION = 6;

    /** @var string */
    protected $expectedVideoFile;

    /** @var string */
    protected $expectedCmdLineQuiet;

    /** @var string */
    protected $expectedCmdLineVerbose;

    /** @var string */
    protected $downloadVideo;

    /** @var string */
    protected $destinationFolder;

    /** @var \App\Media */
    protected $media;

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

    public function test_downloaded_file_path_should_be_good(): void
    {
        $this->assertEquals(
            $this->expectedVideoFile,
            DownloadYTMedia::init($this->media, $this->destinationFolder, false)->downloadedFilePath(),
            'expected file {' . $this->expectedVideoFile . '} should be there'
        );
    }

    /** @test */
    public function command_line_should_be_good(): void
    {
        $expectedCommandLine = "/usr/local/bin/yt-dlp --no-warnings --extract-audio --audio-format mp3 --output '/tmp/%(id)s.%(ext)s' --quiet https://www.youtube.com/watch?v=qfx6yf8pux4 >/dev/null 2>&1";
        $this->assertEquals(
            $expectedCommandLine,
            DownloadYTMedia::init($this->media, $this->destinationFolder, false)->commandLine()
        );
    }

    public function test_existing_file_should_be_removed_before_download(): void
    {
        $expectedFileToBeRemovedBefore = $this->destinationFolder . '/' . self::MARIO_COIN_VIDEO . '.mp4';
        // creating fake file
        touch($expectedFileToBeRemovedBefore);
        $this->assertFileExists($expectedFileToBeRemovedBefore);
        DownloadYTMedia::init($this->media, $this->destinationFolder, false);
        $this->assertFileDoesNotExist(
            $expectedFileToBeRemovedBefore,
            'Mp4 file should have been revoed first, before youtube-dl'
        );
    }

    /**
     * This will test that we throw an exception destination folder is not valid (writable and exists).
     */
    public function test_that_we_fail_if_destination_path_is_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DownloadYTMedia::init($this->media, '/path/that/does/not/exists');
    }

    /**
     * This will test that we throw an exception if mediaid is not valid.
     */
    public function test_that_we_fail_if_media_is_invalid(): void
    {
        $foolishMedia = factory(Media::class)->create(['media_id' => 'invalid-media-forever']);
        $this->expectException(DownloadMediaFailureException::class);
        DownloadYTMedia::init($foolishMedia, $this->destinationFolder)->download();
    }
}
