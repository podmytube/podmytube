<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeChannelHasNoVideoException;
use App\Exceptions\YoutubeQueryFailureException;
use App\Factories\YoutubeLastVideoFactory;
use App\Quota;
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

    public function testGettingLastVideoShouldBeGood()
    {
        /**
         * this factory is getting the last channel media info+tags,
         * then it is storing the total quota consumption
         */
        $factory = YoutubeLastVideoFactory::forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID);
        $lastMedia = $factory->lastMedia();

        /** checking results */
        $this->assertEquals('EePwbhMqEh0', $lastMedia['media_id']);
        $this->assertEqualsCanonicalizing(['dev', 'podmytube'], $lastMedia['tags']);

        /** checking quota consumption has been persisted */
        $quotaModel = Quota::first();
        $this->assertEquals(12, $quotaModel->quota_used);
        $this->assertEquals(YoutubeLastVideoFactory::SCRIPT_NAME, $quotaModel->script);
    }

    public function testChannelWithNoVideosShouldThrowException()
    {
        $this->expectException(YoutubeChannelHasNoVideoException::class);
        YoutubeLastVideoFactory::forChannel('UCq80IvL314jsE7PgYsTdw7Q'); // accropolis replays (strangely)
    }

    public function testGettingInvalidMediaShouldFail()
    {
        $this->expectException(YoutubeQueryFailureException::class);
        YoutubeLastVideoFactory::forChannel('ChannelWhichWillNeverExists');
    }
}
