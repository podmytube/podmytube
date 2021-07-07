<?php

declare(strict_types=1);

use App\Modules\MediaProperties;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class MediaPropertiesTest extends TestCase
{
    /**
     * diamonds from Rihanna, extracted from YT.
     */
    public const audioFile = __DIR__.'/../Fixtures/Audio/lWA2pjMjpBs.mp3';
    public const mariocoin = __DIR__.'/../Fixtures/Audio/qfx6yf8pux4.mp3';

    public function test_get_audio_file_duration(): void
    {
        $expectedDuration = 283;
        $this->assertEquals($expectedDuration, $result = MediaProperties::analyzeFile(self::audioFile)->duration());
    }

    public function test_shorter_file_duration_is_good(): void
    {
        $expectedDuration = 5;
        $expectedFilesize = 86469;
        $mediaProperties = MediaProperties::analyzeFile(self::mariocoin);

        $this->assertEquals($expectedDuration, $mediaProperties->duration());
        $this->assertEquals($expectedFilesize, $mediaProperties->filesize());
    }

    public function test_get_audio_file_size(): void
    {
        $expectedFileSize = 4527888;
        $this->assertEquals($expectedFileSize, MediaProperties::analyzeFile(self::audioFile)->filesize());
    }

    public function test_invalid_file_throw_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->audioFileInfos = MediaProperties::analyzeFile(__FILE__);
    }
}
