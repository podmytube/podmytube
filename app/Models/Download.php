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

    public const INTERVAL_PER_DAY = 0;
    public const INTERVAL_PER_WEEK = 1;
    public const INTERVAL_PER_MONTHG = 2;

    protected $dates = [];

    protected $casts = [
        // 'log_day' => 'date:Y-m-d', // mutated below
        'counted' => 'integer',
    ];

    protected $guarded = ['id'];

    public static function forChannelThisDay(Channel $channel, Carbon $date): int
    {
        return self::query()
            ->forChannel($channel)
            ->where('log_day', '=', $date->toDateString())
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

    public static function sumOfDownloadsForChannelDuringPeriod(Channel $channel, Carbon $startDate, Carbon $endDate): int
    {
        return intval(Download::query()
            ->forChannel($channel)
            ->duringPeriod($startDate, $endDate)
            ->sum('counted'))
        ;
    }

    public static function sumOfDownloadsForMediaDuringPeriod(Media $media, Carbon $startDate, Carbon $endDate): int
    {
        return intval(Download::query()->where('media_id', '=', $media->id)
            ->duringPeriod($startDate, $endDate)
            ->sum('counted'))
        ;
    }

    public static function downloadsByInterval(
        Carbon $startDate,
        Carbon $endDate,
        ?Channel $channel = null,
        ?int $interval = null,
    ): Collection {
        $interval ??= self::INTERVAL_PER_DAY;

        $query = Download::query()
            ->select('log_day')
        ;

        $query->selectRaw('sum(counted) as counted');

        if ($startDate->toDateString() === $endDate->toDateString()) {
            $query->where('log_day', '=', $startDate->toDateString());
        } else {
            $query->duringPeriod($startDate, $endDate);
        }

        $groupsBy = ['log_day'];
        if ($channel !== null) {
            $groupsBy[] = 'channel_id';
        }
        $query->groupBy($groupsBy);

        return $query->get();
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

        if ($moreThan !== null) {
            $query->having('aggregate', '>=', $moreThan);
        }

        return $query->get();
    }

    /*
    |--------------------------------------------------------------------------
    | scopes
    |--------------------------------------------------------------------------
    */
    public function scopeDuringPeriod(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        if ($startDate > $endDate) {
            throw new InvalidStartDateException('Start date should be before end date !');
        }

        return $query
            ->whereBetween('log_day', [$startDate->toDateString(), $endDate->toDateString()])
        ;
    }

    public function scopeForChannel(Builder $query, Channel $channel): Builder
    {
        return $query->where('Channel_id', '=', $channel->channel_id);
    }

    protected function logDay(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->toDateString(),
            set: fn ($value) => Carbon::parse($value)->toDateString(),
        );
    }
}
