<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
     * Vignette suffix
     */
    public const _VIGNETTE_SUFFIX = '_vig';

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
     * This function is returning the thumb data.
     * 
     * @return string content of the file.
     */
    public function getData()
    {
        return Storage::disk($this->file_disk)->get($this->relativePath());
    }

    /**
     * This function is checking if one thumbnail is existing for a specific channel.
     *
     * @return boolean true if thumb present false else.
     */
    public function exists()
    {
        return Storage::disk($this->file_disk)->exists($this->relativePath());
    }

    /**
     * This function is checking if one thumbnail is existing for a specific channel.
     *
     * @return boolean true if thumb present false else.
     */
    public function vignetteExists()
    {
        return Storage::disk($this->file_disk)->exists($this->vignetteRelativePath());
    }

    /**
     * This function is returning the vignette file name.
     *
     * @return string vignette file name
     */
    public function vignetteRelativePath()
    {
        list($fileName, $fileExtension) = explode('.', $this->file_name);
        return $this->channelPath() . $fileName . self::_VIGNETTE_SUFFIX . '.' . $fileExtension;
    }


    /**
     * This function return the relative path (on the disk) of the thumb.
     * 
     * @return string relative path (channel_id/thumb.jpg)
     */
    public function relativePath()
    {
        return $this->channelPath() . $this->file_name;
    }

    /**
     * This function will return the channel path.
     * 
     * @return string relative path of the channel (where to store thumbs)
     */
    public function channelPath ()
    {
        return $this->channel_id . DIRECTORY_SEPARATOR;
    }

    /**
     * return the url of the thumbs for the current channel.
     * 
     * @return string thumb url to be used in the feed
     */
    public function podcastUrl()
    {
        return getenv('THUMBS_URL') . DIRECTORY_SEPARATOR . $this->relativePath();
    }

    /**
     * If the thumb exist return the internal url else return the default one.
     * 
     * @return string thumb url to be used in the dashboard
     */
    public function dashboardUrl()
    {
        return Storage::disk($this->file_disk)->url($this->relativePath());
    }

    /**
     * If the thumb exist return the internal url else return the default one.
     * 
     * @return string thumb url to be used in the dashboard
     */
    public function vignetteUrl()
    {
        return Storage::disk($this->file_disk)->url($this->vignetteRelativePath());
    }

    /**
     * return the url of the default thumb.
     * 
     * @return string default thumb url to be used in the dashboard
     */
    public static function defaultUrl()
    {
        return Storage::disk(self::_STORAGE_DISK)->url(self::_DEFAULT_THUMB_FILE);
    }

    /**
     * return the url of the default vignette.
     * 
     * @return string default vignette url to be used in the dashboard
     */
    public static function defaultVignetteUrl()
    {
        return Storage::disk(self::_STORAGE_DISK)->url(self::_DEFAULT_VIGNETTE_FILE);
    }
}
