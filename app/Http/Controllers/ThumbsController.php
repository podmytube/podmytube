<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Events\ThumbUpdated;
use App\Thumb;
use Illuminate\Http\Request;

class ThumbsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel)
    {
        if (!isset($channel->thumb)) {
            $thumb_url = Thumb::defaultUrl();
        } else {
            $thumb_url = $channel->thumb->dashboardUrl();
        }
        return view('thumbs.index', compact('channel', 'thumb_url'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Channel $channel)
    {
        return $this->edit($channel);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Channel $channel)
    {
        /** error messages to be translated */
        $messages = [
            'required' => __('messages.thumbs_edit_error_image_required'),
            'dimensions' => __('messages.thumbs_edit_error_image_dimensions'),
        ];

        /** requirements for podcast thumb */
        $rules = [
            'new_thumb_file' =>
                'required|dimensions:min_width=1400,min_height=1400,max_width=3000,max_height=3000,ratio=1',
        ];
        $this->validate($request, $rules, $messages);

        if (!$request->file('new_thumb_file')->isValid()) {
            throw new \Exception('A problem occurs during new thumb upload !');
        }

        /** attaching uploaded thumb to channel */
        $thumb = Thumb::make()->attachItToChannel(
            $request->file('new_thumb_file'),
            $channel
        );

        event(ThumbUpdated::shouldUpdateChannel($thumb->channel));

        return redirect()->route('channel.thumbs.index', [
            'channel' => $channel,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thumb  $thumb
     * @return \Illuminate\Http\Response
     */
    public function show(Thumb $thumb)
    {
        $this->index($thumb->channel);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Channel  $channel
     * @return \Illuminate\Http\Response
     */
    public function edit(Channel $channel)
    {
        return view('thumbs.edit', compact('channel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thumb  $thumb
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thumb $thumb)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thumb  $thumbs
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thumb $thumb)
    {
        //
    }
}
