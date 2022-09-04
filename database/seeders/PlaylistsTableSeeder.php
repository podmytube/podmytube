<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Playlist;

class PlaylistsTableSeeder extends LocalSeeder
{
    public const GAGNER_DE_L_ARGENT_SUR_INTERNET = 'PL8hP2MDWYfPzeDQFgMvcGjJIm3kZqT2BS';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateTables('playlists');

        // create channel
        Playlist::create([
            'channel_id' => static::JEANVIET_CHANNEL_ID,
            'youtube_playlist_id' => self::GAGNER_DE_L_ARGENT_SUR_INTERNET,
            'title' => "Gagner de l'argent sur internet.",
            'description' => 'lorem ipsum',
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
