<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubePlaylists;
use App\Youtube\YoutubeQuotas;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubePlaylistsTest extends TestCase
{
    protected const MY_PERSONAL_UPLOADS_PLAYLIST_ID = 'UUw6bU9JT_Lihb2pbtqAUGQw';

    /** @var \App\Interfaces\QuotasCalculator quotaCalculator */
    protected $quotaCalculator;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->quotaCalculator = new YoutubeQuotas();
    }

    public function testChannelIsGettingTheRightUploadsPlaylist()
    {
        $this->assertEquals(
            self::MY_PERSONAL_UPLOADS_PLAYLIST_ID,
            ($playlists = YoutubePlaylists::init($this->quotaCalculator))
                ->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID)
                ->uploadsPlaylistId()
        );
        $this->assertEquals(3, $playlists->quotasUsed());
    }

    public function testChannelIsGettingTheRightFavoritesPlaylist()
    {
        $this->assertEquals(
            'FLw6bU9JT_Lihb2pbtqAUGQw',
            ($playlists = YoutubePlaylists::init($this->quotaCalculator))
                ->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID)
                ->favoritesPlaylistId()
        );
        $this->assertEquals(3, $playlists->quotasUsed());
    }

    public function testInvalidChannelIdShouldThrowAnException()
    {
        $this->expectException(YoutubeNoResultsException::class);
        YoutubePlaylists::init($this->quotaCalculator)
            ->forChannel('ForSureThisChannelWillNeverEverExist')
            ->uploadsPlaylistId();
    }
}
