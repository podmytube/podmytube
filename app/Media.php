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

    public const DISK = 'medias';
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

    public function relativePath()
    {
        return $this->channel_id .
            DIRECTORY_SEPARATOR .
            $this->media_id .
            self::FILE_EXTENSION;
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
    public function scopeGrabbedAt(
        Builder $query
    ) {
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
    public function fileExists(): bool
    {
        return Storage::disk(self::DISK)->exists($this->relativePath());
    }

    public function scopeGrabbedBefore(Builder $query, Carbon $date)
    {
        return $query->whereDate('grabbed_at', '<', $date);
    }
}
