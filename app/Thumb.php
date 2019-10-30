<?php

namespace App;

use App\Exceptions\ThumbUploadHasFailedException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Thumb extends Model
{
    /** @var string _LOCAL_STORAGE_DISK where thumbs and vigs are stored locally */
    public const _LOCAL_STORAGE_DISK = 'thumbs';

    /** @var string _REMOTE_STORAGE_DISK where thumbs and vigs are stored remotely */
    public const _REMOTE_STORAGE_DISK = 'sftpthumbs';

    /** @var string _DEFAULT_THUMB_FILE default thumb file (1400x1400) and default vignette one. */
    public const _DEFAULT_THUMB_FILE = 'default_thumb.jpg';

    protected $fillable = [
        'channel_id',
        'file_name',
        'file_disk',
        'file_size',
    ];

    /**
     * extra attribute relativePath.
     */
    public function getRelativePathAttribute()
    {
        return $this->channel_id . '/' . $this->file_name;
    }

    /** alias for getRelativePathAttribute */
    public function relativePath()
    {
        return $this->getRelativePathAttribute();
    }

    /**
     * This function defines the relation between one thumb and its channel (the channel it is belonging to)  .
     * @return Object Channel
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'channel_id');
    }

    /**
     * This function is returning the data of the relative img path specified.
     * 
     * @return string content of the file.
     */
    public function getData()
    {
        return Storage::disk($this->file_disk)->get($this->relativePath);
    }

    /**
     * getter filename function
     */
    public function fileName()
    {
        return $this->file_name;
    }

    /**
     * getter filedisk function
     */
    public function fileDisk()
    {
        return $this->file_disk;
    }

    /**
     * getter channel_id function
     */
    public function channelId()
    {
        return $this->channel_id;
    }

    /**
     * This function is checking if one thumbnail is existing for a specific channel.
     *
     * @return boolean true if thumb present false else.
     */
    public function exists()
    {
        return Storage::disk($this->file_disk)->exists($this->relativePath);
    }

    /**
     * return the url of the thumbs for the current channel.
     * 
     * @return string thumb url to be used in the feed
     */
    public function podcastUrl()
    {
        return getenv('THUMBS_URL') . DIRECTORY_SEPARATOR . $this->relativePath;
    }

    /**
     * If the thumb exist return the internal url else return the default one.
     * 
     * @return string thumb url to be used in the dashboard
     */
    public function dashboardUrl()
    {
        return Storage::disk($this->file_disk)->url($this->relativePath);
    }

    /**
     * return the url of the default thumb.
     * 
     * @return string default thumb url to be used in the dashboard
     */
    public static function defaultUrl()
    {
        return getenv('THUMBS_URL') . DIRECTORY_SEPARATOR . self::_DEFAULT_THUMB_FILE;
    }

    /**
     * This function will upload thum to thumb server.
     * 
     */
    public function upload()
    {
        try {
            /** 
             * put is taking 2 arguments 
             * - the relative path from SFTP_THUMBS_PATH where to store data 
             * - the file content (data)
             */
            Storage::disk(self::_REMOTE_STORAGE_DISK)
                ->put(
                    $this->relativePath,
                    $this->getData()
                );

            /** Once uploaded, we are setting the channel_path on the remote to public visibility  */
            Storage::disk(self::_REMOTE_STORAGE_DISK)
                ->setVisibility($this->channelId(), 'public');
        } catch (\Exception $e) {
            Log::alert("Uploading image " . $this->relativePath . " on remote image repository has failed with message {{$e->getMessage()}}.");
            throw $e;
        }
    }

    /**
     * This function will remove the current thumb file.
     * Should be done within a queue.
     */
    public function delete()
    {
        /** removing local vig */
        return Storage::disk($this->fileDisk())->delete($this->relativePath);
    }


    public function attachItToChannel(UploadedFile $uploadedFile, Channel $channel)
    {
        try {
            dump($channel);
            dd([
                'channel_id' => $channel->channelId(),
                'file_size' => $uploadedFile->getSize(),
                /** get filename of the stored file */
                'file_name' => pathinfo(
                    $uploadedFile->store(
                        $channel->channelId(),
                        Thumb::_LOCAL_STORAGE_DISK
                    ),
                    PATHINFO_FILENAME
                ),
                'file_disk' => Thumb::_LOCAL_STORAGE_DISK,
            ]);
            $result = $this->updateOrCreate(
                [
                    'channel_id' => $channel->channelId(),
                ],
                [
                    'channel_id' => $channel->channelId(),
                    'file_size' => $uploadedFile->getSize(),
                    /** get filename of the stored file */
                    'file_name' => pathinfo(
                        $uploadedFile->store(
                            $channel->channelId(),
                            Thumb::_LOCAL_STORAGE_DISK
                        ),
                        PATHINFO_FILENAME
                    ),
                    'file_disk' => Thumb::_LOCAL_STORAGE_DISK,
                ]
            );
        } catch (\Exception $e) { 
            throw new ThumbUploadHasFailedException("thumb upload has failed with error : ".$e->getMessage());
        }
        return $result;
    }
}
