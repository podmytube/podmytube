<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use Tests\TestCase;
use Tests\Traits\IsFakingYoutube;

/**
 * @internal
 * @coversNothing
 */
class YoutubeTestCase extends TestCase
{
    use IsFakingYoutube;

    public const PERSONAL_UPLOADS_PLAYLIST_ID = 'UUw6bU9JT_Lihb2pbtqAUGQw';
    public const PERSONAL_CHANNEL_NB_OF_PLAYLISTS = 2;
    public const PEWDIEPIE_CHANNEL_ID = 'UC-lHJZR3Gqxm24_Vd_AJ5Yw';
    public const PEWDIEPIE_UPLOADS_PLAYLIST_ID = 'UU-lHJZR3Gqxm24_Vd_AJ5Yw';
    public const NOWTECH_UPLOADS_PLAYLIST_ID = 'UUVwG9JHqGLfEO-4TkF-lf2g';
    public const NOWTECH_PLAYLIST_ID = 'PLhQHoIKUR5vD0vq6Jwns89QAz9OZWTvpx';

    public const CHANNELS_ENDPOINT = 'https://www.googleapis.com/youtube/v3/channels';
    public const PLAYLISTS_ENDPOINT = 'https://www.googleapis.com/youtube/v3/playlists';
    public const PLAYLIST_ITEMS_ENDPOINT = 'https://www.googleapis.com/youtube/v3/playlistItems';
    public const VIDEOS_ENDPOINT = 'https://www.googleapis.com/youtube/v3/videos';

    public const CHANNELS_INDEX = 'channels';
    public const PLAYLISTS_INDEX = 'playlists';
    public const PLAYLIST_ITEMS_INDEX = 'playlistItems';
    public const VIDEOS_INDEX = 'videos';

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
    }
}
