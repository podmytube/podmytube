<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Channel;
use Carbon\Carbon;

class Media extends Model
{
    protected $table = 'medias';

    /**
     * define the relationship between media and its channel
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'channel_id');
    }

    /**
     * define a scope to get medias grabbed between 2 dates.
     * @param object query is the query object
     * @param array value should have 2 date in it [0] is the startDate, [1] is the endDate
     */
    public function scopeGrabbedBetween(
        $query,
        Carbon $startDate,
        Carbon $endDate
    ) {

        if ($startDate > $endDate) {
            throw new \Exception("Start date should be before end date !");
        }

        return $query->whereBetween(
            'grabbed_at',
            [
                $startDate->toDateString(),
                $endDate->toDateString()
            ]
        );
    }

    /**
     * define a scope to get medias published between 2 dates.
     * @param object query is the query object
     * @param array value should have 2 date in it [0] is the startDate, [1] is the endDate
     */
    public function scopePublishedBetween(
        $query,
        Carbon $startDate,
        Carbon $endDate
    ) {

        if ($startDate > $endDate) {
            throw new \Exception("Start date should be before end date !");
        }

        return $query->whereBetween(
            'published_at',
            [
                $startDate->toDateString(),
                $endDate->toDateString()
            ]
        );
    }
}