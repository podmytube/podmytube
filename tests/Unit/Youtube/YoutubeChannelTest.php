<?php

namespace Tests\Unit\Youtube;

use App\ApiKey;
use App\Youtube\YoutubeChannel;
use App\Youtube\YoutubeCore;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeChannelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->apikey = ApiKey::make()->get();
        $this->youtubeCore = YoutubeCore::init($this->apikey);
    }

    public function testPewDiePieShouldExistsForLong()
    {
        $youtubeChannelObj = YoutubeChannel::init(
            $this->youtubeCore
        )->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID);

        $this->assertTrue($youtubeChannelObj->exists());

        $this->assertEquals('PewDiePie', $youtubeChannelObj->name());
    }

    public function testThisOneShouldNotExistsAtAll()
    {
        $this->assertFalse(
            YoutubeChannel::init($this->youtubeCore)
                ->forChannel(
                    'Je-Doute-Que-Ce-Channel-Existe-Un-Jour-Meme-Lointain-Pour-De-Vrai'
                )
                ->exists()
        );
    }
}
