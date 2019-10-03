<?php

namespace App\Http\Controllers;


use App\Thumb;

use App\Channel;
use App\Jobs\SendThumbBySFTP;
use Illuminate\Http\Request;
use App\Services\ThumbService;

class ThumbsController extends Controller
{
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
        return view(
            'thumbs.index',
            compact('channel', 'thumb_url')
        );
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
        $messages = [
            'required' => __('messages.thumbs_edit_error_image_required'),
            'dimensions' => __('messages.thumbs_edit_error_image_dimensions'),
        ];
        $rules = [
            'new_thumb_file' => 'required|dimensions:min_width=1400,min_height=1400,max_width=3000,max_height=3000'
        ];
        $this->validate($request, $rules, $messages);

        if (!$request->file('new_thumb_file')->isValid()) {
            throw new \Exception("A problem occurs during new thumb upload !");
        }

        try {
            /**
             * upload thumb on local storage
             */
            $thumbService = ThumbService::create();
            $thumbService->addUploadedThumb($request->file('new_thumb_file'), $channel);
            /**
             * create vignette from local storage
             */
            $thumbService->createThumbVig($channel);
            /**
             * This process will add the job SendThumbBySFTP to the queue (upload is long).
             * Jobs are runned with one supervisor.
             * !! IMPORTANT !! 
             * QUEUE_DRIVER in .env should be set to database and commands 
             * php artisan queue:table
             * php artisan migrate
             * should have been run
             */
            SendThumbBySFTP::dispatch($channel->thumb)->delay(now()->addMinutes(1));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }


        return redirect()->route('channel.thumbs.index', ['channel' => $channel]);
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
