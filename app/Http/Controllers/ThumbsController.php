<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Events\ThumbUpdated;
use App\Http\Requests\ThumbRequest;
use App\Interfaces\Coverable;
use App\Playlist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ThumbsController extends Controller
{
    public function channelCoverUpdate(ThumbRequest $request, Channel $channel)
    {
        $this->authorize('update', $channel);
        return $this->coverUpdate($request->file('new_thumb_file'), $channel);
    }

    public function playlistCoverUpdate(ThumbRequest $request, Playlist $playlist)
    {
        $this->authorize('update', $playlist);
        return $this->coverUpdate($request->file('new_thumb_file'), $playlist);
    }

    public function channelCoverEdit(Request $request, Channel $channel)
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

    protected function coverUpdate(UploadedFile $uploadedFile, Coverable $coverable)
    {
        if (!$uploadedFile->isValid()) {
            Log::error("A problem occurs during new thumb upload for {$coverable->nameWithId()}!");
            throw new Exception('A problem occurs during new thumb upload !');
        }
        $thumb = $coverable->setCoverFromUploadedFile($uploadedFile);

        ThumbUpdated::dispatch($thumb->coverable);

        return redirect()->route('home')->with('success', 'Your cover has been updated 🎉.');
    }
}
