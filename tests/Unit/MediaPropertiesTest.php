<?php

use App\Modules\MediaProperties;
use Tests\TestCase;

class MediaPropertiesTest extends TestCase
{
    /**
     * diamonds from Rihanna, extracted from YT
     */
    const audioFile = __DIR__ . '/../fixtures/Audio/lWA2pjMjpBs.mp3';

    /**
     * This will test if getDuration is working well.
     * @return void
     */
    public function testGetAudioFileDuration()
    {
        $expectedDuration = 283;
        $this->assertEquals(
            $expectedDuration,
            $result = MediaProperties::analyzeFile(self::audioFile)->duration(),
            "Expected duration was {$expectedDuration} and we obtained {$result}."
        );
    }

    /**
     * This will test if getFilesize is working well.
     * @return void
     */
    public function testGetAudioFileSize()
    {
        $expectedFileSize = 4527888;
        $this->assertEquals(
            $expectedFileSize,
            $result = MediaProperties::analyzeFile(self::audioFile)->filesize(),
            "Expected duration was {$expectedFileSize} and we obtained {$result}."
        );
    }

    public function testInvalidFileThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->audioFileInfos = MediaProperties::analyzeFile(__FILE__);
    }
}
