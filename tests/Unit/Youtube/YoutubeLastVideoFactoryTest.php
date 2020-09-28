<?php

namespace Tests\Unit\Youtube;

use App\Factories\YoutubeLastVideoFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeLastVideoFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testGettingLastVideo()
    {
        $factory = YoutubeLastVideoFactory::forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID);

        $lastMedia = $factory->lastMedia();
        dump($lastMedia);
        $this->assertEquals('EePwbhMqEh0', $lastMedia['media_id']);
        $this->assertEqualsCanonicalizing(['dev', 'podmytube'], $lastMedia['tags']);
        $this->assertEquals(12, $factory->quotasConsumed());
    }
}
