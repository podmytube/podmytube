<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\Coverable;
use App\Traits\BelongsToChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Thumb extends Model
{
    use BelongsToChannel;
    use HasFactory;

    public const LOCAL_STORAGE_DISK = 'thumbs';
    public const DEFAULT_THUMB_FILE = 'default_thumb.jpg';

    protected $guarded = [];

    public function coverable()
    {
        return $this->morphTo();
    }

    // alias for getRelativePathAttribute
    public function relativePath(): ?string
    {
        if ($this->coverable) {
            return $this->coverable->channelId() . '/' . $this->file_name;
        }

        return null;
    }

    /**
     * This function is returning the data of the relative img path specified.
     *
     * @return string content of the file
     */
    public function getData()
    {
        return Storage::disk($this->file_disk)->get($this->relativePath());
    }

    /**
     * getter filename function.
     */
    public function fileName()
    {
        return $this->file_name;
    }

    /**
     * Check if thumb file exists.
     *
     * @return bool true if thumb present false else
     */
    public function exists()
    {
        return $this->relativePath() !== null && Storage::disk($this->file_disk)->exists($this->relativePath());
    }

    /**
     * return the url of the thumbs for the current channel.
     *
     * @return string thumb url to be used in the feed
     */
    public function podcastUrl()
    {
        return config('app.thumbs_url') . '/' . $this->relativePath();
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
     * return the url of the default thumb.
     *
     * @return string default thumb url to be used in the dashboard
     */
    public static function defaultUrl()
    {
        return config('app.thumbs_url') . '/' . self::DEFAULT_THUMB_FILE;
    }

    public function localFilePath()
    {
        return Storage::disk(self::LOCAL_STORAGE_DISK)->path($this->relativePath());
    }

    public function remoteFilePath()
    {
        return config('app.thumbs_path') . $this->relativePath();
    }

    public function setCoverable(Coverable $coverable)
    {
        return $this->update(
            [
                'coverable_type' => get_class($coverable),
                'coverable_id' => $coverable->id(),
            ]
        );
    }

    /**
     * Label to be used in error logs/message.
     */
    public function coverableLabel(): string
    {
        return get_class($this->coverable) . "::find({$this->coverable->id()})";
    }
}
