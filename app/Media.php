<?php

namespace App;

use App\Exceptions\CreatingChannelFolderException;
use App\Exceptions\InvalidStartDateException;
use App\Exceptions\PermissionException;
use App\Exceptions\UploadingMediaException;
use App\Modules\EnclosureUrl;
use App\Traits\BelongsToChannel;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use BelongsToChannel, SoftDeletes;

    public const UPLOADED_BY_USER_DISK = 'uploadedMedias';
    public const REMOTE_DISK = 'medias';
    public const FILE_EXTENSION = '.mp3';

    /** @var string $table medias table name - without it fails */
    protected $table = 'medias';

    /** @var string $primaryKey if only I had set id as prim key */
    protected $primaryKey = 'media_id';

    /** @var bool $incrementing come with my fucking legacy media_id */
    public $incrementing = false;

    protected $guarded = [];
    /**
     * those fields are converted into Carbon mutator
     */
    protected $dates = [
        'published_at',
        'grabbed_at',
        'created_at',
        'updated_at',
    ];

    /**
     * get media url with trait HasFile
     */
    public function mediaUrl()
    {
        return $this->enclosureUrl();
    }

    public function mediaFileName()
    {
        return $this->media_id . self::FILE_EXTENSION;
    }

    public function relativePath()
    {
        return $this->mediaFolder() . '/' . $this->mediaFileName();
    }

    public function mediaFolder()
    {
        return $this->channel_id;
    }

    /**
     * define a scope to get medias grabbed between 2 dates.
     *
     * @param object query is the query object
     * @param array value should have 2 date in it [0] is the startDate, [1] is the endDate
     */
    public function scopeGrabbedBetween(
        $query,
        Carbon $startDate,
        Carbon $endDate
    ) {
        if ($startDate > $endDate) {
            throw new InvalidStartDateException(
                'Start date should be before end date !'
            );
        }

        return $query->whereBetween('grabbed_at', [
            $startDate->toDateString(),
            $endDate->toDateString(),
        ]);
    }

    /**
     * define a scope to get medias published between 2 dates.
     *
     * @param Illuminate\Database\Eloquent\Builder query is the query object
     * @param array value should have 2 date in it [0] is the startDate, [1] is the endDate
     */
    public function scopePublishedBetween(
        Builder $query,
        Carbon $startDate,
        Carbon $endDate
    ) {
        if ($startDate > $endDate) {
            throw new InvalidStartDateException(
                'Start date should be before end date !'
            );
        }

        return $query->whereBetween('published_at', [
            $startDate->toDateTimeString(),
            $endDate->toDateTimeString(),
        ]);
    }

    /**
     * define a scope to get medias that are grabbed.
     *
     * @param Illuminate\Database\Eloquent\Builder query is the query object
     */
    public function scopeGrabbedAt(Builder $query)
    {
        return $query->whereNotNull('grabbed_at');
    }

    /**
     * scope episodes published last month.
     */
    public function scopePublishedLastMonth(Builder $query)
    {
        return $query
            ->publishedBetween(
                Carbon::now()
                    ->startOfDay()
                    ->subMonth()
                    ->startOfMonth()
                    ->subDay(),
                Carbon::now()
                    ->startOfDay()
                    ->subMonth()
                    ->endOfMonth()
            )
            ->orderBy('published_at', 'desc');
    }

    public function enclosureUrl()
    {
        return EnclosureUrl::create($this)->get();
    }

    public function pubDate()
    {
        return $this->published_at->timezone('Europe/Paris')->format(DATE_RSS);
    }

    public function duration()
    {
        return $this->duration;
    }

    public function id() :string
    {
        return $this->media_id;
    }

    /**
     * check if a media has already been grabbed
     */
    public function hasBeenGrabbed()
    {
        return $this->grabbed_at !== null;
    }

    /**
     * check if media file is really there.
     *
     * @return bool true if file really exists
     */
    public function remoteFileExists(): bool
    {
        return Storage::disk(self::REMOTE_DISK)->exists($this->relativePath());
    }

    public function url()
    {
        return config('app.mp3_url') . '/' . $this->remoteFilePath();
    }

    public function checkRemotePerms()
    {
        $folderExists = Storage::disk(self::REMOTE_DISK)->exists($this->mediaFolder());

        if ($folderExists) {
            // channel folder exists and has right permissions
            $folderVisibility = Storage::disk(self::REMOTE_DISK)->getVisibility($this->mediaFolder());
            if ($folderVisibility == 'public') {
                return true;
            }
            return $this->setRemoteMediaFolderPublic();
        }

        $this->createRemoteMediaFolder();

        $this->setRemoteMediaFolderPublic();

        return true;
    }

    public function createRemoteMediaFolder()
    {
        $createDirResult = Storage::disk(self::REMOTE_DISK)->makeDirectory($this->mediaFolder());
        if ($createDirResult === false) {
            throw new CreatingChannelFolderException("Creating {$this->mediaFolder()} on remote has failed.");
        }
        return true;
    }

    public function setRemoteMediaFolderPublic()
    {
        $permissionsResult = Storage::disk(self::REMOTE_DISK)->setVisibility($this->mediaFolder(), 'public');
        if ($permissionsResult === false) {
            throw new PermissionException("Setting visibility for {$this->mediaFolder()} on remote has failed.");
        }
        return true;
    }

    public function uploadFromPath(string $filePath)
    {
        $this->checkRemotePerms();
        try {
            Storage::disk(self::REMOTE_DISK)->putFileAs(
                $this->mediaFolder(),
                $filePath,
                $this->mediaFileName(),
                'public'
            );
        } catch (Exception $exception) {
            $message = "Uploading file $filePath to {$this->mediaFolder()} for {$this->channel->nameWithId()}on remote has failed with {$exception->getMessage()}";
            Log::error($message);
            throw new UploadingMediaException($message);
        }
        return true;
    }

    public function scopeGrabbedBefore(Builder $query, Carbon $date)
    {
        return $query->whereDate('grabbed_at', '<', $date);
    }

    public static function byMediaId(string $mediaId):?self
    {
        return self::where('media_id', '=', $mediaId)->first();
    }

    public function uploadedFilePath()
    {
        return Storage::disk(self::UPLOADED_BY_USER_DISK)->path($this->mediaFileName());
    }

    public function remoteFilePath()
    {
        return config('app.mp3_path') . $this->relativePath();
    }

    public function toPodcastItem()
    {
        return  [
            'guid' => $this->media_id,
            'title' => $this->title,
            'enclosureUrl' => $this->enclosureUrl(),
            'mediaLength' => $this->length,
            'pubDate' => $this->pubDate(),
            'description' => $this->description,
            'duration' => $this->duration(),
            'explicit' => $this->channel->podcastExplicit(),
        ];
    }

    public static function youtubeUrl(string $mediaId)
    {
        return 'https://www.youtube.com/watch?v=' . $mediaId;
    }

    public function youtubeWatchUrl()
    {
        return self::youtubeUrl($this->media_id);
    }
}
