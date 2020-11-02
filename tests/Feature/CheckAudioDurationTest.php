<?php

namespace Tests\Feature;

use App\Exceptions\YoutubeAndLocalDurationException;
use App\Modules\CheckAudioDuration;
use Tests\TestCase;

class CheckAudioDurationTest extends TestCase
{
    /**
     * diamonds from Rihanna, extracted from YT
     */
    const audioFile = __DIR__ . '/../fixtures/Audio/lWA2pjMjpBs.mp3';

    public function testCheckDurationIsWorkingFine()
    {
        // exact duration = 283
        $this->assertTrue(CheckAudioDuration::init(283, self::audioFile)->check());
        // little less
        $this->assertTrue(CheckAudioDuration::init(278, self::audioFile)->check());
        // little more
        $this->assertTrue(CheckAudioDuration::init(288, self::audioFile)->check());

        // too big difference
        $this->expectException(YoutubeAndLocalDurationException::class);
        CheckAudioDuration::init(300, self::audioFile)->check();
    }
}
