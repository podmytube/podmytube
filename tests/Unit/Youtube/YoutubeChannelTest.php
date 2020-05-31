<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubeChannel;
use App\Youtube\YoutubeQuotas;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeChannelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testPewDiePieShouldExistsForLong()
    {
        $this->assertTrue(
            ($channel = new YoutubeChannel())
                ->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)
                ->exists()
        );

        $this->assertEquals('PewDiePie', $channel->name());
        $this->assertEquals(
            3,
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
