<?php

namespace App\Http\Controllers;


use App\Thumb;

use App\Channel;

use Illuminate\Http\Request;
use App\Services\ThumbService;

class ThumbsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, Request $request)
    {
        $thumb_url = ThumbService::getChannelThumbUrl($channel);

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

        /**
         * new_thumb_file is the form field
         * UploadedFile::store get 2 parameters 
         *      - the folder we want to save in and 
         *      - the disk (config/filesystems) 
         * and return a unique filepath we split to get filename
         */
        
        ThumbService::create()->addUploadedThumb($request->file('new_thumb_file'), $channel);
        $file_path = $request->file('new_thumb_file')
            ->store($channel->channel_id, Thumb::_STORAGE_DISK);

        $file_name = explode(DIRECTORY_SEPARATOR, $file_path)[1];
        
        // storing new/updated entry in db
        $newThumb = Thumb::updateOrCreate(
            [
                'channel_id' => $channel->channel_id
            ],
            [
                'channel_id' => $channel->channel_id,
                'file_name' => $file_name,
                'file_disk' => Thumb::_STORAGE_DISK,
                'file_size' => \Storage::disk(Thumb::_STORAGE_DISK)->size($file_path),
            ]
        );

        try {
            ThumbService::createThumbVig($newThumb);
        } catch (\Exception $e) {
            throw new \Exception("A problem occurs during new thumb upload !");
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
        $this->index();
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
