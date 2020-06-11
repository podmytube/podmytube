<?php

namespace App;

use App\Exceptions\InvalidStartDateException;
use App\Modules\EnclosureUrl;
use App\Modules\PeriodsHelper;
use App\Traits\BelongsToChannel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use BelongsToChannel;

    protected $table = 'medias';
    protected $primaryKey = 'media_id';
    public $incrementing = false;

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
     * will get
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
}
