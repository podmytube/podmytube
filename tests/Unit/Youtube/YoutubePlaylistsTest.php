<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubePlaylists;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class YoutubePlaylistsTest extends TestCase
{
    use RefreshDatabase;

    protected const MY_PERSONAL_UPLOADS_PLAYLIST_ID = 'UUw6bU9JT_Lihb2pbtqAUGQw';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function test_invalid_channel_id_should_throw_an_exception(): void
    {
        $this->expectException(YoutubeNoResultsException::class);
        (new YoutubePlaylists())
            ->forChannel('ForSureThisChannelWillNeverEverExist')
        ;
    }

    public function test_playlists_is_ok(): void
    {
        $playlists = (new YoutubePlaylists())->forChannel(self::PERSONAL_CHANNEL_ID)->playlists();
        $this->assertCount(2, $playlists);
        $this->assertArrayHasKey('PLQz1j9ftwaG0h5LWWnqgMYPo31PlXZcko', $playlists);
        $this->assertEquals('testPmt', $playlists['PLQz1j9ftwaG0h5LWWnqgMYPo31PlXZcko']['title']);
        $this->assertEquals(0, $playlists['PLQz1j9ftwaG0h5LWWnqgMYPo31PlXZcko']['nbVideos']);
    }
}
