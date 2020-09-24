<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public const FREE_PLAN_ID = 1;
    public const EARLY_PLAN_ID = 2;
    public const PROMO_MONTHLY_PLAN_ID = 3; // old 6€/month plan
    public const PROMO_YEARLY_PLAN_ID = 4; // old 66€/month plan
    public const WEEKLY_PLAN_ID = 5;
    public const DAILY_PLAN_ID = 6;
    public const ACCROPOLIS_PLAN_ID = 7; // to be removed one day
    public const DEFAULT_PLAN_ID = self::FREE_PLAN_ID;
    public const WEEKLY_PLAN_PROMO_ID = 8;
    public const DAILY_PLAN_PROMO_ID = 9;

    /**
     * One plan may be subscribed by many channels.
     *
     * @return object App\Subscription
     */
    public function subscriptions()
    {
        return $this->HasMany(Subscription::class);
    }

    /**
     * @return object App\StripePlan
     */
    public function stripePlan()
    {
        return $this->HasMany(StripePlan::class);
    }

    public function scopeFree(Builder $query)
    {
        return $query->where('id', '=', self::FREE_PLAN_ID);
    }

    public function scopePaying(Builder $query)
    {
        return $query->whereNotIn('id', [
            self::FREE_PLAN_ID,
            self::EARLY_PLAN_ID,
        ]);
    }

    public static function byIds(array $planIds)
    {
        return static::whereIn('plans.id', $planIds)
            ->join('stripe_plans', function ($join) {
                $join
                    ->on('stripe_plans.plan_id', '=', 'plans.id')
                    ->where(
                        'stripe_plans.is_live',
                        '=',
                        env('APP_ENV') === 'production' ? true : false
                    );
            })
            ->orderBy('price', 'ASC')
            ->get();
    }

    public static function bySlug(string $slug)
    {
        return self::where('slug', '=', strtolower($slug))->first();
    }
}
