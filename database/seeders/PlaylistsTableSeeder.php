<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Playlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class PlaylistsTableSeeder extends Seeder
{
    public const GAGNER_DE_L_ARGENT_SUR_INTERNET = 'PL8hP2MDWYfPzeDQFgMvcGjJIm3kZqT2BS';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (!App::environment('local')) {
            return false;
        }

        DB::table('playlists')->delete();

        // create channel
        Playlist::create([
            'channel_id' => ChannelsTableSeeder::JEANVIET_CHANNEL_ID,
            'youtube_playlist_id' => self::GAGNER_DE_L_ARGENT_SUR_INTERNET,
            'title' => "Gagner de l'argent sur internet.",
            'description' => 'lorem ipsum',
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
