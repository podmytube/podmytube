<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubeChannel;
use App\Youtube\YoutubeQuotas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeChannelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testPewDiePieShouldExistsForLong()
    {
        $channel = new YoutubeChannel();
        $this->assertTrue(
            $channel
                ->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)
                ->exists()
        );

        $this->assertEquals('PewDiePie', $channel->name());
        $expectedQuota = [$channel->apikey() => 3];
        $this->assertEqualsCanonicalizing(
            $expectedQuota,
            YoutubeQuotas::forUrls($channel->queriesUsed())->quotaConsumed()
        );
    }

    public function testGettingManyChannelsequence()
    {
        $channel = new YoutubeChannel();
        $this->assertTrue(
            $channel
                ->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)
                ->exists()
        );
        $this->assertEquals('PewDiePie', $channel->name());

        $this->assertTrue(
            $channel->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID)->exists()
        );
        $this->assertEquals('Frédérick Tyteca', $channel->name());

        $expectedQuota = [$channel->apikey() => 6];
        $this->assertEqualsCanonicalizing(
            $expectedQuota,
            YoutubeQuotas::forUrls($channel->queriesUsed())->quotaConsumed()
        );
    }

    public function testThisOneShouldNotExistsAtAll()
    {
        $this->expectException(YoutubeNoResultsException::class);
        (new YoutubeChannel())
            ->forChannel(
                'Je-Doute-Que-Ce-Channel-Existe-Un-Jour-Meme-Lointain-Pour-De-Vrai'
            )
            ->exists();
    }
}
