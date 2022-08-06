<?php

declare(strict_types=1);

namespace App;

use App\Traits\BelongsToPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class StripePlan extends Model
{
    use BelongsToPlan;
    use HasFactory;

    public $timestamps = false;
    public $casts = [
        'is_yearly' => 'boolean',
    ];

    public function scopeIsYearly(Builder $query)
    {
        return $query->where('is_yearly', '=', true);
    }

    public static function stripeIdsOnly(): Collection
    {
        $query = self::query();
        if (App::isProduction()) {
            $query->select('stripe_live_id as stripe_id');
        } else {
            $query->select('stripe_test_id as stripe_id');
        }

        return $query->get()->pluck('stripe_id');
    }

    /**
     * getting stripe price id according to mode (live/test) and frequency.
     */
    public static function priceIdForPlanAndBilling(Plan $plan, bool $isYearly = false, bool $isLive = true): string
    {
        $query = self::query();
        if ($isLive) {
            $query->select('stripe_live_id as stripe_id');
        } else {
            $query->select('stripe_test_id as stripe_id');
        }

        return $query->where([
            ['plan_id', '=', $plan->id],
            ['is_yearly', '=', $isYearly],
        ])
            ->first()
            ->stripe_id
        ;
    }

    public static function byStripeId(string $stripeId, bool $isLive = true): ?self
    {
        return self::query()->when($isLive, function ($query) use ($stripeId) {
            return $query->where('stripe_live_id', '=', $stripeId);
        }, function ($query) use ($stripeId) {
            return $query->where('stripe_test_id', '=', $stripeId);
        })->first();
    }
}
