<?php

use App\Modules\MediaProperties;
use Tests\TestCase;

class MediaPropertiesTest extends TestCase
{
    /**
     * diamonds from Rihanna, extracted from YT
     */
    const audioFile = __DIR__ . '/../fixtures/Audio/lWA2pjMjpBs.mp3';
    const mariocoin = __DIR__ . '/../fixtures/Audio/qfx6yf8pux4.mp3';

    public function testGetAudioFileDuration()
    {
        $expectedDuration = 283;
        $this->assertEquals($expectedDuration, $result = MediaProperties::analyzeFile(self::audioFile)->duration());
    }

    public function testShorterFileDurationIsGood()
    {
        $expectedDuration = 5;
        $expectedFilesize = 86469;
        $mediaProperties = MediaProperties::analyzeFile(self::mariocoin);

        $this->assertEquals($expectedDuration, $mediaProperties->duration());
        $this->assertEquals($expectedFilesize, $mediaProperties->filesize());
    }

    public function testGetAudioFileSize()
    {
        $expectedFileSize = 4527888;
        $this->assertEquals($expectedFileSize, MediaProperties::analyzeFile(self::audioFile)->filesize());
    }

    public function testInvalidFileThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->audioFileInfos = MediaProperties::analyzeFile(__FILE__);
    }
}
