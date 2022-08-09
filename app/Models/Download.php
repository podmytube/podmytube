<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToChannel;
use App\Traits\BelongsToMedia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use BelongsToChannel;
    use BelongsToMedia;
    use HasFactory;

    protected $dates = [];

    protected $casts = [
        'log_day' => 'datetime:Y-m-d',
        'count' => 'integer',
    ];

    protected $guarded = ['id'];

    public static function forChannelThisDay(Channel $channel, Carbon $date): int
    {
        return self::where(
            [
                ['log_day', '=', $date->toDateString()],
                ['channel_id', '=', $channel->channel_id],
            ]
        )
            ->sum('count')
        ;
    }

    public static function forMediaThisDay(Media $media, Carbon $date): int
    {
        return self::where(
            [
                ['log_day', '=', $date->toDateString()],
                ['media_id', '=', $media->id],
            ]
        )
            ->first()->count
        ;
    }
}
