<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

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

    protected $casts = [
        'price' => 'integer',
    ];

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
    public function stripePlans()
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
            ->join('stripe_plans', function ($join): void {
                $join
                    ->on('stripe_plans.plan_id', '=', 'plans.id')
                    ->where(
                        'stripe_plans.is_live',
                        '=',
                        env('APP_ENV') === 'production' ? true : false
                    )
                ;
            })
            ->orderBy('price', 'ASC')
            ->get()
        ;
    }

    public function scopeSlug(Builder $query, string $slug)
    {
        return $query->where('slug', '=', $slug);
    }

    public static function bySlug(string $slug)
    {
        return (new static())->slug($slug)->first();
    }

    public function scopeSlugs(Builder $query, array $slugs)
    {
        return $query->whereIn('slug', $slugs);
    }

    public static function bySlugs(array $slugs): ?Collection
    {
        $results = (new static())->slugs($slugs)->get();
        if (!$results->count()) {
            return null;
        }

        return $results;
    }

    public function scopeWithYearlyStripePlans(Builder $query)
    {
        return $query->whereHas('stripePlan', function (Builder $query) {
            return $query->where('is_yearly', '=', true);
        });
    }

    public static function onlyYearly(): ?Collection
    {
        $results = (new static())->withYearlyStripePlans()->get();
        if (!$results->count()) {
            return null;
        }

        return $results;
    }

    public static function bySlugsAndBillingFrequency(array $slugs, ?bool $isYearly = true): ?Collection
    {
        if (!count($slugs)) {
            throw new InvalidArgumentException('You should give some slugs to get plans');
        }

        return Plan::with(
            [
                'stripePlan' => function ($query) use ($isYearly): void {
                    $query->where('is_yearly', '=', $isYearly);
                },
            ]
        )
            ->slugs($slugs)
            ->get()
        ;
    }
}
