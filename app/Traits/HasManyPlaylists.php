<?php

namespace App\Traits;

use App\Playlist;

trait HasManyPlaylists
{
    /**
     * define the relationship between one channel and its playlists
     */
    public function playlists()
    {
        return $this->HasMany(Playlist::class, 'channel_id');
    }
}
