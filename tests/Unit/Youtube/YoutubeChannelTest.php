<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubeChannel;
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

    /** @test */
    public function youtube_channel_name_is_ok()
    {
        $this->assertEquals('PewDiePie', $this->youtubeChannel->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)->name());
    }

    /** @test */
    public function youtube_channel_exists_is_ok()
    {
        $this->assertTrue($this->youtubeChannel->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)->exists());

        $this->expectException(YoutubeNoResultsException::class);
        $this->youtubeChannel->forChannel('Je-Doute-Que-Ce-Channel-Existe-Un-Jour-Meme-Lointain-Pour-De-Vrai')->exists();
    }

    public function testAnotherWayToObtainUploadsPlaylistId()
    {
        $this->assertEquals(
            YoutubeCoreTest::PERSONAL_UPLOADS_PLAYLIST_ID,
            $this->youtubeChannel->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID, ['id', 'snippet', 'contentDetails'])->uploadsPlaylistId()
        );
    }
}
