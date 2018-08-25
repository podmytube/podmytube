<?php

namespace App;

use App\Channel;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class MediasStats extends Model
{
    
    public $timestamps = false;

    /**
    * this function define the relationship between medias_stats and channels
    */
	public function channel()
	{
        return $this->belongsTo(Channel::class, 'channel_id');
    }
    
    
    /**
     * define a scope to get stats for one channel
     * @param object query is the query object
     * @param string the channel_id
     */
    public function scopeChannelId($query, $channel_id)
    {

        return $query->where('channel_id', $channel_id);

    }

    
    /**
     * define a scope to get stats on the last N days (7, 15, 30, custom value)
     * @param object query is the query object
     * @param integer the nb of days before yesterday
     */
    public function scopeLastNDays($query, $value){

        if ( !is_numeric($value) || $value <= 0) 

        {

            throw new \Exception("Last N days should be one integer greater than 0");

        }

        $start_date = Carbon::yesterday()->subDays($value);
        $end_date = Carbon::yesterday();

        return $query->whereBetween('media_day', [$start_date->toDateString(), $end_date->toDateString()]);
    }


    /**
     * define a scope to get stats between some dates
     * @param object query is the query object
     * @param array value should have 2 date in it [0] is the startDate, [1] is the endDate
     */
    public function scopeBetweenDays($query, $value){

        try {

            $start_date = new Carbon($value[0]);
            $end_date = new Carbon($value[1]);

        } catch (\Exception $e) {

            throw new \Exception("start and end parameters should be formatted date like '2018-12-25'");

        }

        if ( $start_date > $end_date ) 

        {

            throw new \Exception("Start date should be before end date !");

        }

        return $query->whereBetween('media_day', [$start_date->toDateString(), $end_date->toDateString()]);
    }

}
