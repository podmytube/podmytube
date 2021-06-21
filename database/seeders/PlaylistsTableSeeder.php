<?php

namespace Database\Seeders;

use App\Channel;
use App\Playlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class PlaylistsTableSeeder extends Seeder
{
    public const GAGNER_DE_L_ARGENT_SUR_INTERNET = 'PL8hP2MDWYfPzeDQFgMvcGjJIm3kZqT2BS';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!App::environment('local')) {
            return false;
        }

        DB::table('playlists')->delete();

        /** create channel */
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
