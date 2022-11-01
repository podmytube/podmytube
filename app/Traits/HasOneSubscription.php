<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

trait HasOneSubscription
{
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'channel_id');
    }

    public function hasSubscription(): bool
    {
        return $this->subscription !== null;
    }

    public function subscribeToPlan(Plan $plan): Subscription
    {
        return Subscription::updateOrCreate(
            ['channel_id' => $this->channel_id],
            ['plan_id' => $plan->id]
        );
    }

    public function plan(): HasOneThrough
    {
        return $this->hasOneThrough(
            Plan::class,
            Subscription::class,
            'channel_id',
            'id',
            'channel_id',
            'plan_id',
        );
    }
}
