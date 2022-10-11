<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\ThumbUpdatedEvent;
use App\Http\Requests\ThumbRequest;
use App\Interfaces\Coverable;
use App\Jobs\CreateVignetteFromThumbJob;
use App\Models\Channel;
use App\Models\Playlist;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

class ThumbsController extends Controller
{
    public function channelCoverUpdate(ThumbRequest $request, Channel $channel)
    {
        $this->authorize('update', $channel);

        try {
            return $this->coverUpdate($request->file('new_thumb_file'), $channel);
        } catch (Throwable $thrown) {
            Log::error($thrown->getMessage());

            return redirect()->route('home')->withErrors(['danger' => $thrown->getMessage()]);
        }
    }

    public function playlistCoverUpdate(ThumbRequest $request, Playlist $playlist)
    {
        $this->authorize('update', $playlist);

        try {
            return $this->coverUpdate($request->file('new_thumb_file'), $playlist);
        } catch (Throwable $thrown) {
            Log::error($thrown->getMessage());

            return redirect()->route('home')->withErrors(['danger' => $thrown->getMessage()]);
        }
    }

    public function channelCoverEdit(Channel $channel)
    {
        $this->authorize('update', $channel);
        $coverable = $channel;
        $objectType = 'channel';

        return view('thumbs.edit', compact('coverable', 'objectType'));
    }

    public function playlistCoverEdit(Playlist $playlist)
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

        // dispatching create vignette job
        CreateVignetteFromThumbJob::dispatch($thumb);

        // dispatching thumbUpdated event
        ThumbUpdatedEvent::dispatch($thumb->coverable);

        return redirect()->route('home')->with('success', 'Your cover has been updated 🎉. If not visible may I suggest you to refresh this page ?.');
    }
}
