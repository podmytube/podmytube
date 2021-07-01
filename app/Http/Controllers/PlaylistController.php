<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Playlist;

class PlaylistController extends Controller
{
    public function edit(Playlist $playlist)
    {
        $this->authorize($playlist);

        return view('playlist.edit', compact('playlist'));
    }
}
