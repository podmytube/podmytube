<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubeChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class YoutubeChannelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Youtube\YoutubeChannel */
    protected $youtubeChannel;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->youtubeChannel = new YoutubeChannel();
    }

    /** @test */
    public function youtube_channel_name_is_ok(): void
    {
        $this->assertEquals('PewDiePie', $this->youtubeChannel->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)->name());
    }

    /** @test */
    public function youtube_channel_exists_is_ok(): void
    {
        $this->assertTrue($this->youtubeChannel->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)->exists());

        $this->expectException(YoutubeNoResultsException::class);
        $this->youtubeChannel->forChannel('Je-Doute-Que-Ce-Channel-Existe-Un-Jour-Meme-Lointain-Pour-De-Vrai')->exists();
    }

    public function test_another_way_to_obtain_uploads_playlist_id(): void
    {
        $this->assertEquals(
            YoutubeCoreTest::PERSONAL_UPLOADS_PLAYLIST_ID,
            $this->youtubeChannel->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID, ['id', 'snippet', 'contentDetails'])->uploadsPlaylistId()
        );
    }
}
