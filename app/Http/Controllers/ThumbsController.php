<?php

namespace App\Http\Controllers;


use App\Thumbs;

use App\Channel;

use Image;

use Illuminate\Http\Request;

class ThumbsController extends Controller
{

    protected $file_disk = 'thumbs';

    protected $thumb_side = 300; // it s a square

    protected $dashboard_thumb_filename = 'dashboard_thumb.jpg';


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel)
    {

        $thumb = $channel->thumbs()->first();

        $thumb_url = $thumb->get_url();
        
        return view('thumbs.index', compact('channel','thumb', 'thumb_url'));

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

        
        if (! $request->file('new_thumb_file')->isValid()) {

            throw new \Exception("A problem occurs during new thumb upload !");
          
        } 

        /**
         * new_thumb_file is the form field
         * store get 2 parameters 
         *      - the folder we want to save in and 
         *      - the disk (config/filesystems) 
         * and return a unique filepath we split to get filename
         */
        $file_path = $request->file('new_thumb_file')->store($channel->channel_id, $this->file_disk);
        
        $file_name = explode( DIRECTORY_SEPARATOR , $file_path)[1];
        
        // storing new/updated entry in db
        Thumbs::updateOrCreate 
        (
            [   
                'channel_id' => $channel->channel_id
            ],
            [
                'channel_id' => $channel->channel_id,
                'file_name' => $file_name,
                'file_disk' => $this->file_disk,
                'file_size' => \Storage::disk($this->file_disk)->size($file_path),
            ]
        );

        // mini thumb to be used in dashboard creation
        $thumb_path = $channel->channel_id . DIRECTORY_SEPARATOR . $this->dashboard_thumb_filename;

        $thumbnail = Image::make($request->file('new_thumb_file'));

        $thumbnail->fit($this->thumb_side, $this->thumb_side, function ($constraint) {
            $constraint->aspectRatio();
        });
    
        \Storage::disk($this->file_disk)->put($thumb_path, (string) $thumbnail->encode());

        return redirect()->route('channel.thumbs.index', ['channel' => $channel]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thumbs  $thumbs
     * @return \Illuminate\Http\Response
     */
    public function show(Thumbs $thumbs)
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
     * @param  \App\Thumbs  $thumbs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thumbs $thumbs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thumbs  $thumbs
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thumbs $thumbs)
    {
        //
    }
}
