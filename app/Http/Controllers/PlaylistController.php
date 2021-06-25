<?php

namespace App\Http\Controllers;

use App\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function edit(Request $request, Playlist $playlist)
    {
        $this->authorize($playlist);
        return view('playlist.edit', compact('playlist'));
    }
}
