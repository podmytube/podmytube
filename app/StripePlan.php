<?php

namespace App;

use App\Traits\BelongsToPlan;
use Illuminate\Database\Eloquent\Model;

class StripePlan extends Model
{
    use BelongsToPlan;

    public const _PROMO_MONTHLY_PLAN_TEST = 'plan_EfYDgsuNMdj8Sb';
    public const _PROMO_MONTHLY_PLAN_PROD = 'plan_EcuGg9SyUBw97i';

    public const _PROMO_YEARLY_PLAN_TEST = 'plan_EfYBFztmlQ3u4C';
    public const _PROMO_YEARLY_PLAN_PROD = 'plan_EcuJ2npV5EMrCg';

    public const _WEEKLY_PLAN_TEST = 'plan_EfudBu6TCXHWEg';
    public const _WEEKLY_PLAN_PROD = 'plan_EaIv2XTMGtuY5g';

    public const _DAILY_PLAN_TEST = 'plan_EfuceKVUwJTt5O';
    public const _DAILY_PLAN_PROD = 'plan_DFsB9U76WaSaR3';

    public const _ACCROPOLIS_PLAN_TEST = 'plan_EfubS6xkc5amyO';
    public const _ACCROPOLIS_PLAN_PROD = 'plan_Ecv3k67W6rsSKk';
}
