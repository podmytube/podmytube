<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubeChannel;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeChannelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testGettingVideosForMyChannelShouldBeOk()
    {
        $this->assertCount(
            YoutubeCoreTest::PERSONAL_CHANNEL_NB_OF_PLAYLISTS,
            YoutubeChannel::forChannel(
                YoutubeCoreTest::PERSONAL_CHANNEL_ID
            )->videos()
        );
    }

    public function testPewDiePieShouldExistsForLong()
    {
        $youtubeChannelObj = YoutubeChannel::forChannel(
            YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID
        );

        $this->assertTrue($youtubeChannelObj->exists());

        $this->assertEquals('PewDiePie', $youtubeChannelObj->name());
    }

    public function testThisOneShouldNotExistsAtAll()
    {
        $this->expectException(YoutubeNoResultsException::class);
        YoutubeChannel::forChannel(
            'Je-Doute-Que-Ce-Channel-Existe-Un-Jour-Meme-Lointain-Pour-De-Vrai'
        )->exists();
    }
}
