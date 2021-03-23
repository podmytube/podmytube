<?php

namespace App;

use App\Exceptions\InvalidStartDateException;
use App\Modules\EnclosureUrl;
use App\Traits\BelongsToChannel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use BelongsToChannel, SoftDeletes;

    public const UPLOADED_BY_USER_DISK = 'uploadedMedias';
    public const REMOTE_DISK = 'medias';
    public const FILE_EXTENSION = '.mp3';

    public const STATUS_NOT_DOWNLOADED = 0;
    public const STATUS_DOWNLOADED = 1;
    public const STATUS_TAG_FILTERED = 10; // filtered by tag
    public const STATUS_AGE_FILTERED = 11; // filtered too old
    public const STATUS_NOT_PROCESSED_ON_YOUTUBE = 20; // upcoming
    public const STATUS_NOT_AVAILABLE_ON_YOUTUBE = 21; // should not be possible unless deleted after being registered in pod
    public const STATUS_EXHAUSTED_QUOTA = 99; // user has more episode to be converted but is not paying enough for

    /** @var string $table medias table name - without it fails */
    protected $table = 'medias';

    /** @var string $primaryKey if only I had set id as prim key */
    protected $primaryKey = 'media_id';

    /** @var bool $incrementing come with my fucking legacy media_id */
    public $incrementing = false;

    protected $guarded = ['id'];
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
    public function mediaUrl(): string
    {
        return $this->enclosureUrl();
    }

    public function mediaFileName(): string
    {
        return $this->media_id . self::FILE_EXTENSION;
    }

    public function relativePath(): string
    {
        return $this->mediaFolder() . '/' . $this->mediaFileName();
    }

    public function mediaFolder(): string
    {
        return $this->channel_id;
    }

    /**
     * define a scope to get medias grabbed between 2 dates.
     *
     * @param object query is the query object
     * @param array value should have 2 date in it [0] is the startDate, [1] is the endDate
     */
    public function scopeGrabbedBetween($query, Carbon $startDate, Carbon $endDate): Builder
    {
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
    public function scopePublishedBetween(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
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
    public function scopeGrabbedAt(Builder $query): Builder
    {
        return $query->whereNotNull('grabbed_at');
    }

    /**
     * scope episodes published last month.
     */
    public function scopePublishedLastMonth(Builder $query): Builder
    {
        return $query
            ->publishedBetween(
                Carbon::now()->startOfDay()->subMonth()->startOfMonth()->subDay(),
                Carbon::now()->startOfDay()->subMonth()->endOfMonth()
            )
            ->orderBy('published_at', 'desc');
    }

    public function enclosureUrl(): string
    {
        return EnclosureUrl::create($this)->get();
    }

    public function pubDate(): string
    {
        return $this->published_at->timezone('Europe/Paris')->format(DATE_RSS);
    }

    public function duration(): int
    {
        return $this->duration;
    }

    public function id(): string
    {
        return $this->media_id;
    }

    public function isGrabbed(): bool
    {
        return $this->grabbed_at !== null;
    }

    /**
     * check if a media has already been grabbed
     */
    public function hasBeenGrabbed(): bool
    {
        return $this->grabbed_at !== null;
    }

    public function url(): string
    {
        return config('app.mp3_url') . '/' . $this->remoteFilePath();
    }

    public function scopeGrabbedBefore(Builder $query, Carbon $date): Builder
    {
        return $query->whereDate('grabbed_at', '<', $date);
    }

    public static function byMediaId(string $mediaId): ?self
    {
        return self::where('media_id', '=', $mediaId)->first();
    }

    public function uploadedFilePath(): string
    {
        return Storage::disk(self::UPLOADED_BY_USER_DISK)->path($this->mediaFileName());
    }

    public function remoteFilePath(): string
    {
        return config('app.mp3_path') . $this->relativePath();
    }

    public function toPodcastItem()
    {
        return [
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

    public function titleWithId()
    {
        return "{$this->title} ({$this->media_id})";
    }

    public function statusComment()
    {
        $comments = [
            self::STATUS_NOT_DOWNLOADED => "Episode {$this->title} has not been downloaded yet.",
            self::STATUS_DOWNLOADED => "Episode {$this->title} has been added to your podcast.",
            self::STATUS_TAG_FILTERED => "Episode {$this->title} has been filtered by tag ",
            self::STATUS_AGE_FILTERED => "Episode {$this->title} is too old to be included to your podcast.",
            self::STATUS_NOT_PROCESSED_ON_YOUTUBE => "Episode {$this->title} is not available yet on Youtube (upcoming live ?)",
            self::STATUS_NOT_AVAILABLE_ON_YOUTUBE => "Episode {$this->title} is unknow on Youtube. Did you remove it ?",
            self::STATUS_EXHAUSTED_QUOTA => 'Your quota has been exhausted this month. What about upgrading ?',
        ];
        return $comments[$this->status];
    }
}
