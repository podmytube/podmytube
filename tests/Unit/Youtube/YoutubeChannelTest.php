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
    public function invalid_channel_id_should_throw_exception(): void
    {
        $this->expectException(YoutubeNoResultsException::class);
        YoutubeChannel::init()->forChannel('invalid-channel_id');
    }

    /** @test */
    public function obtaining_name_from_valid_channel_id_should_succeed(): void
    {
        $this->assertEquals('PewDiePie', $this->youtubeChannel->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)->name());
    }

    public function test_another_way_to_obtain_uploads_playlist_id(): void
    {
        $this->assertEquals(
            YoutubeCoreTest::PERSONAL_UPLOADS_PLAYLIST_ID,
            $this->youtubeChannel->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID, ['id', 'snippet', 'contentDetails'])->uploadsPlaylistId()
        );
    }
}
