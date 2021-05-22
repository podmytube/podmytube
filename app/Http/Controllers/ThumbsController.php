<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Events\ThumbUpdated;
use App\Http\Requests\ThumbRequest;
use App\Playlist;
use App\Thumb;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ThumbsController extends Controller
{
    public function channelCoverUpdate(ThumbRequest $request, Channel $channel)
    {
        $this->authorize('update', $channel);

        if (!$request->file('new_thumb_file')->isValid()) {
            Log::error("A problem occurs during new thumb upload for {$channel->nameWithId()}!");
            throw new Exception('A problem occurs during new thumb upload !');
        }

        /** attaching uploaded thumb to channel */
        $thumb = Thumb::make()->attachItToChannel($request->file('new_thumb_file'), $channel);

        ThumbUpdated::dispatch($thumb->channel);

        return redirect()->route('home')->with('success', 'Your cover has been updated 🎉.');
    }

    public function channelCoverEdit(Channel $channel)
    {
        $this->authorize('update', $channel);
        $coverable = $channel;
        $objectType = 'channel';
        return view('thumbs.edit', compact('coverable', 'objectType'));
    }

    public function playlistCoverEdit(Request $request, Playlist $playlist)
    {
        $this->authorize('any', $playlist);
        $coverable = $playlist;
        $objectType = 'playlist';
        return view('thumbs.edit', compact('coverable', 'objectType'));
    }
}
