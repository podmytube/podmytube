<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public const FREE_PLAN_ID = 1;
    public const EARLY_PLAN_ID = 2;
    public const PROMO_MONTHLY_PLAN_ID = 3;
    public const PROMO_YEARLY_PLAN_ID = 4;
    public const WEEKLY_PLAN_ID = 5;
    public const DAILY_PLAN_ID = 6;
    public const ACCROPOLIS_PLAN_ID = 7; // to be removed one day
    public const DEFAULT_PLAN_ID = self::FREE_PLAN_ID;

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
     * @return object App\Subscription
     */
    public function stripePlan()
    {
        return $this->HasMany(StripePlan::class);
    }
}
