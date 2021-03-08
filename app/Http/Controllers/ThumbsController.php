<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Events\ThumbUpdated;
use App\Http\Requests\ThumbRequest;
use App\Thumb;
use Exception;
use Illuminate\Support\Facades\Log;

class ThumbsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel)
    {
        $this->authorize('view', $channel);

        if (!isset($channel->thumb)) {
            $thumb_url = Thumb::defaultUrl();
        } else {
            $thumb_url = $channel->thumb->dashboardUrl();
        }
        return view('thumbs.index', compact('channel', 'thumb_url'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ThumbRequest $request, Channel $channel)
    {
        Log::debug(self::class . '::' . __FUNCTION__ . ' - start');
        $this->authorize('update', $channel);

        if (!$request->file('new_thumb_file')->isValid()) {
            Log::error("A problem occurs during new thumb upload for {$channel->nameWithId()}!");
            throw new Exception('A problem occurs during new thumb upload !');
        }

        /** attaching uploaded thumb to channel */
        $thumb = Thumb::make()->attachItToChannel($request->file('new_thumb_file'), $channel);

        ThumbUpdated::dispatch($thumb->channel);

        return redirect()->route('home');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Channel $channel
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Channel $channel)
    {
        $this->authorize('update', $channel);
        return view('thumbs.edit', compact('channel'));
    }
}
