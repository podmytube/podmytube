<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubeChannel;
use App\Youtube\YoutubeQuotas;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeChannelTest extends TestCase
{
    /** @var \App\Interfaces\QuotasCalculator quotaCalculator */
    protected $quotaCalculator;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->quotaCalculator = new YoutubeQuotas();
    }

    public function testPewDiePieShouldExistsForLong()
    {
        $this->assertTrue(
            ($channel = YoutubeChannel::init($this->quotaCalculator))
                ->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)
                ->exists()
        );

        $this->assertEquals('PewDiePie', $channel->name());
        $this->assertEquals(3, $channel->quotasUsed());
    }

    public function testThisOneShouldNotExistsAtAll()
    {
        $this->expectException(YoutubeNoResultsException::class);
        ($channel = YoutubeChannel::init($this->quotaCalculator))
            ->forChannel(
                'Je-Doute-Que-Ce-Channel-Existe-Un-Jour-Meme-Lointain-Pour-De-Vrai'
            )
            ->exists();
        $this->assertEquals(3, $channel->quotasUsed());
    }
}
