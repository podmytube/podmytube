<?php

namespace App;

use App\Traits\BelongsToPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StripePlan extends Model
{
    use BelongsToPlan;

    public $timestamps = false;
    public $casts = [
        'is_yearly' => 'boolean',
    ];

    public const PROMO_MONTHLY_PLAN_TEST = 'plan_EfYDgsuNMdj8Sb'; // old 6€/month
    public const PROMO_MONTHLY_PLAN_PROD = 'plan_EcuGg9SyUBw97i'; // old 6€/month

    public const PROMO_YEARLY_PLAN_TEST = 'plan_EfYBFztmlQ3u4C'; // old 66€/year
    public const PROMO_YEARLY_PLAN_PROD = 'plan_EcuJ2npV5EMrCg'; // old 66€/year

    public const WEEKLY_PLAN_TEST = 'plan_EfudBu6TCXHWEg'; // 9€/month
    public const WEEKLY_PLAN_PROD = 'plan_EaIv2XTMGtuY5g'; // 9€/month

    public const DAILY_PLAN_TEST = 'plan_EfuceKVUwJTt5O'; // 29€/month
    public const DAILY_PLAN_PROD = 'plan_DFsB9U76WaSaR3'; // 29€/month

    public const ACCROPOLIS_PLAN_TEST = 'plan_EfubS6xkc5amyO'; // 6€/month
    public const ACCROPOLIS_PLAN_PROD = 'plan_Ecv3k67W6rsSKk'; // 6€/month

    public const PROMO_WEEKLY_PLAN_TEST = 'price_1Gu1xiLrQ8vSqYZEwgjVhGBC'; // 9€ => 6€/month
    public const PROMO_WEEKLY_PLAN_PROD = 'price_1Gu1YPLrQ8vSqYZERxvBFAgu'; // 9€ => 6€/month

    public const PROMO_DAILY_PLAN_TEST = 'price_1Gu1yVLrQ8vSqYZESNvD0bK7'; // 29€ => 25€/month
    public const PROMO_DAILY_PLAN_PROD = 'price_1Gu1nTLrQ8vSqYZEBRGDkeky'; // 29€ => 25€/month

    public function scopeIsYearly(Builder $query)
    {
        return $query->where('is_yearly', '=', true);
    }

    public static function yearly()
    {
        return (new static())->period(true)->get();
    }

    public static function monthly()
    {
        return (new static())->period(false)->get();
    }
}
