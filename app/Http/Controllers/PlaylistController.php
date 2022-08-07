<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\PodcastableCleaning;
use App\Models\Playlist;

class PlaylistController extends Controller
{
    public function edit(Playlist $playlist)
    {
        $this->authorize($playlist);

        return view('playlist.edit', compact('playlist'));
    }

    public function destroy(Playlist $playlist)
    {
        $this->authorize($playlist);

        $savedTitle = $playlist->podcastTitle();

        PodcastableCleaning::dispatch($playlist);

        return redirect(route('home'))
            ->with(
                'success',
                "Your podcast {$savedTitle} is planned for deletion."
            )
        ;
    }
}
