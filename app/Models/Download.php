<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\InvalidStartDateException;
use App\Traits\BelongsToChannel;
use App\Traits\BelongsToMedia;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        // 'log_day' => 'date:Y-m-d', // mutated below
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

    public static function downloadsForChannelsDuringPeriod(Carbon $startDate, Carbon $endDate, ?int $moreThan = null): Collection
    {
        $query = Download::query()
            ->select('channel_id')
            ->selectRaw('sum(counted) as aggregate')
        ;

        if ($startDate->toDateString() === $endDate->toDateString()) {
            $query->where('log_day', '=', $startDate->toDateString());
        } else {
            $query->duringPeriod($startDate, $endDate);
        }
        $query->groupBy('channel_id');

        if ($moreThan != null) {
            $query->having('aggregate', '>=', $moreThan);
        }

        return $query->get();
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
        return Download::query()
            ->select('log_day', 'counted')
            ->where('channel_id', '=', $channel->channel_id)
            ->duringPeriod($startDate, $endDate)
            ->get()
        ;
    }

    protected function logDay(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->toDateString(),
            set: fn ($value) => Carbon::parse($value)->toDateString(),
        );
    }
}
