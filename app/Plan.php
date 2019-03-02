<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    const _FREE_PLAN_ID = 1;
    const _EARLY_BIRD_PLAN_ID = 2;
    const _PROMO_MONTHLY_ID = 3;
    const _PROMO_YEARLY_ID = 4;
    const _WEEKLY_YOUTUBER_PLAN_ID = 5;
    const _DAILY_YOUTUBER_PLAN_ID = 6;
    const _ACCROPOLIS_PLAN_ID = 7; // to be removed one day

    
    public function subscriptions () 
    {
        return $this->HasMany(Subscription::class);
    } 
}
