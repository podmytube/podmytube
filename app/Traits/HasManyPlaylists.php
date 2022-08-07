<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Playlist;

trait HasManyPlaylists
{
    /**
     * define the relationship between one channel and its playlists.
     */
    public function playlists()
    {
        return $this->HasMany(Playlist::class, 'channel_id');
    }
}
