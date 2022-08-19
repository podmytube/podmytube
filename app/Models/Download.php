<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\InvalidStartDateException;
use App\Traits\BelongsToChannel;
use App\Traits\BelongsToMedia;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use BelongsToChannel;
    use BelongsToMedia;
    use HasFactory;

    // protected $table = 'downloads';

    protected $dates = [];

    protected $casts = [
        'log_day' => 'datetime:Y-m-d',
        'counted' => 'integer',
    ];

    protected $guarded = ['id'];

    public static function forChannelThisDay(Channel $channel, Carbon $date): int
    {
        return self::query()
            ->where(
                [
                    ['log_day', '=', $date->toDateString()],
                    ['channel_id', '=', $channel->channel_id],
                ]
            )
            ->sum('counted')
        ;
    }

    public static function forMediaThisDay(Media $media, Carbon $date): int
    {
        $download = self::where(
            [
                ['log_day', '=', $date->toDateString()],
                ['media_id', '=', $media->id],
            ]
        )
            ->first()
        ;

        return $download !== null ? intval($download->counted) : 0;
    }

    public static function downloadsForChannelDuringPeriod(Channel $channel, Carbon $startDate, Carbon $endDate): int
    {
        return intval(Download::query()->where('channel_id', '=', $channel->channel_id)
            ->duringPeriod($startDate, $endDate)
            ->sum('counted'))
        ;
    }

    public static function downloadsForMediaDuringPeriod(Media $media, Carbon $startDate, Carbon $endDate): int
    {
        return intval(Download::query()->where('media_id', '=', $media->id)
            ->duringPeriod($startDate, $endDate)
            ->sum('counted'))
        ;
    }

    public static function downloadsDuringPeriod(Carbon $startDate, Carbon $endDate): int
    {
        $query = Download::query();
        if ($startDate->toDateString() === $endDate->toDateString()) {
            $query->where('log_day', '=', $startDate->toDateString());
        } else {
            $query->duringPeriod($startDate, $endDate);
        }

        return intval($query->sum('counted'));
    }

    public function scopeDuringPeriod(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        if ($startDate > $endDate) {
            throw new InvalidStartDateException('Start date should be before end date !');
        }

        return $query
            ->whereBetween('log_day', [$startDate->toDateString(), $endDate->toDateString()])
        ;
    }

    public static function downloadsForChannelByDay(Channel $channel, Carbon $startDate, Carbon $endDate): Collection
    {
        ray()->showQueries();

        return Download::query()
            ->where('channel_id', '=', $channel->channel_id)
            ->duringPeriod($startDate, $endDate)
            ->get()
        ;
    }
}
