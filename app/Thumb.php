<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thumb extends Model
{
    /**
     * @var 
     */
    public const _TEMP_STORAGE_DISK = 'appTmp';

    /**
     * where thumbs were stored when stored locally
     * @var string _LOCAL_STORAGE_DISK
     */
    public const _STORAGE_DISK = 'thumbs';

    /**
     * default thumb file (1400x1400) and default vignette one.
     * @var string _DEFAULT_THUMB_FILE
     * @var string _DEFAULT_VIGNETTE_FILE
     */
    public const _DEFAULT_THUMB_FILE = 'default_thumb.jpg';
    public const _DEFAULT_VIGNETTE_FILE = 'default_vignette.jpg';
    public const _DEFAULT_VIGNETTE_WIDTH = 300;


    protected $fillable = [
        'channel_id',
        'file_name',
        'file_disk',
        'file_size',
    ];
    /**
     * This function defines the relation between one thumb and its channel (the channel it is belonging to)  .
     * @return Object Channel
     */
    public function channel()
    {

        return $this->belongsTo(Channel::class, 'channel_id', 'channel_id');

    }

    /**
     * This function is checking if one thumbnail is existing for a specific channel.
     *
     * @return boolean true if thumb present false else.
     */
    public function exists()
    {

        return \Storage::disk('thumbs')->exists($this->channel_id . '/' . $this->file_name);

    }

    /**
     * return the url of the thumbs for the current channel.
     */

    public function get_url()
    {

        if (!$this->exists()) {

            throw new \Exception('Thumb for this channel does not exist');

        }

        return \Storage::disk('thumbs')->url($this->channel_id . '/' . $this->file_name);

    }
}
