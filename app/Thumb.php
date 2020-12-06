<?php

namespace App;

use App\Exceptions\ThumbUploadHasFailedException;
use App\Traits\BelongsToChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Thumb extends Model
{
    use BelongsToChannel;

    public const LOCAL_STORAGE_DISK = 'thumbs';
    public const REMOTE_STORAGE_DISK = 'sftpthumbs';
    public const DEFAULT_THUMB_FILE = 'default_thumb.jpg';

    protected $fillable = ['channel_id', 'file_name', 'file_disk', 'file_size'];

    /**
     * extra attribute relativePath.
     */
    public function getRelativePathAttribute()
    {
        return $this->channel_id . '/' . $this->file_name;
    }

    /*
     * alias for getRelativePathAttribute
     */
    public function relativePath()
    {
        return $this->getRelativePathAttribute();
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
     * Check if thumbnail exists
     *
     * @return bool true if thumb present false else.
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
        return getenv('THUMBS_URL') . '/' . $this->relativePath;
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
        return getenv('THUMBS_URL') . '/' . self::DEFAULT_THUMB_FILE;
    }

    /**
     * This function will upload thum to thumb server.
     */
    public function upload()
    {
        /**
         * put is taking 2 arguments
         * - the relative path from SFTP_THUMBS_PATH where to store data
         * - the file content (data)
         */
        Storage::disk(self::REMOTE_STORAGE_DISK)->put(
            $this->relativePath,
            $this->getData()
        );

        /**
         * Once uploaded, we are setting the channel_path
         * on the remote to public visibility
         */
        Storage::disk(self::REMOTE_STORAGE_DISK)->setVisibility(
            $this->channelId(),
            'public'
        );
    }

    /**
     * set/update a new thumb for the specified channel.
     *
     * @param UploadedFile $uploadedFile the uploaded file
     * @param Channel      $channel      to be associated with thumb
     *
     * @return Thumb object
     */
    public function attachItToChannel(
        UploadedFile $uploadedFile,
        Channel $channel
    ): Thumb {
        try {
            $thumb = $this->updateOrCreate(
                ['channel_id' => $channel->channelId()],
                [
                    'file_size' => $uploadedFile->getSize(),
                    /** get filename of the stored file */
                    'file_name' => basename(
                        $uploadedFile->store(
                            $channel->channelId(),
                            self::LOCAL_STORAGE_DISK
                        )
                    ),
                    'file_disk' => self::LOCAL_STORAGE_DISK,
                ]
            );
        } catch (\Exception $exception) {
            throw new ThumbUploadHasFailedException(
                "Attaching thumb to {$channel->channelId()} has failed {$exception->getMessage()}"
            );
        }
        return $thumb;
    }

    public function localFilePath()
    {
        return Storage::disk(self::LOCAL_STORAGE_DISK)->path($this->relativePath());
    }

    public function remoteFilePath()
    {
        return config('app.thumbs_path') . $this->relativePath();
    }
}
