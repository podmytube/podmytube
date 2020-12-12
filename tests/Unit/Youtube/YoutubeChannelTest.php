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

    /** @var \App\Youtube\YoutubeChannel $youtubeChannel */
    protected $youtubeChannel;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->youtubeChannel = new YoutubeChannel();
    }

    public function testPewDiePieShouldExistsForLong()
    {
        $this->assertTrue(
            $this->youtubeChannel
                ->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)
                ->exists()
        );

        $this->assertEquals('PewDiePie', $this->youtubeChannel->name());
        $expectedQuota = [$this->youtubeChannel->apikey() => 3];
        $this->assertEqualsCanonicalizing(
            $expectedQuota,
            YoutubeQuotas::forUrls($this->youtubeChannel->queriesUsed())->quotaConsumed()
        );
    }

    public function testGettingManyChannelsequence()
    {
        $this->assertTrue(
            $this->youtubeChannel
                ->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)
                ->exists()
        );
        $this->assertEquals('PewDiePie', $this->youtubeChannel->name());

        $this->assertTrue(
            $this->youtubeChannel->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID)->exists()
        );
        $this->assertEquals('Frédérick Tyteca', $this->youtubeChannel->name());

        $expectedQuota = [$this->youtubeChannel->apikey() => 6];
        $this->assertEqualsCanonicalizing(
            $expectedQuota,
            YoutubeQuotas::forUrls($this->youtubeChannel->queriesUsed())->quotaConsumed()
        );
    }

    public function testThisOneShouldNotExistsAtAll()
    {
        $this->expectException(YoutubeNoResultsException::class);
        $this->youtubeChannel->forChannel('Je-Doute-Que-Ce-Channel-Existe-Un-Jour-Meme-Lointain-Pour-De-Vrai')->exists();
    }

    public function testAnotherWayToObtainUploadsPlaylistId()
    {
        $expected = 'UUw6bU9JT_Lihb2pbtqAUGQw';
        $this->assertEquals(
            $expected,
            $this->youtubeChannel->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID, ['id', 'snippet', 'contentDetails'])->uploadsPlaylistId()
        );
    }
}
