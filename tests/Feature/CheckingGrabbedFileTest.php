<?php

namespace Tests\Feature;

use App\Exceptions\YoutubeAndLocalDurationException;
use App\Modules\CheckingGrabbedFile;
use App\Modules\MediaProperties;
use Tests\TestCase;

class CheckingGrabbedFileTest extends TestCase
{
    /**
     * diamonds from Rihanna, extracted from YT
     */
    const audioFile = __DIR__ . '/../Fixtures/Audio/lWA2pjMjpBs.mp3';

    public function testCheckDurationIsWorkingFine()
    {
        // exact duration = 283
        $this->assertTrue(CheckingGrabbedFile::init(MediaProperties::analyzeFile(self::audioFile), 283)->check());
        // little less
        $this->assertTrue(CheckingGrabbedFile::init(MediaProperties::analyzeFile(self::audioFile), 278)->check());
        // little more
        $this->assertTrue(CheckingGrabbedFile::init(MediaProperties::analyzeFile(self::audioFile), 288)->check());
        // too big difference
        $this->expectException(YoutubeAndLocalDurationException::class);
        CheckingGrabbedFile::init(MediaProperties::analyzeFile(self::audioFile), 300)->check();
    }

    public function testEmptyYoutubeDurationIsThrowingException()
    {
        $this->expectException(YoutubeAndLocalDurationException::class);
        $this->assertTrue(CheckingGrabbedFile::init(MediaProperties::analyzeFile(self::audioFile))->check());
    }
}
