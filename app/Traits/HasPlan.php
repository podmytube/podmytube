<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Plan;
use App\Models\Subscription;

trait HasPlan
{
    public function plan()
    {
        /* this is creating query
            select
            "plans".*,
            "subscriptions"."channel_id" as "laravel_through_key"
            from
            "plans"
            inner join "subscriptions" on "subscriptions"."plan_id" = "plans"."id"
            where
            "subscriptions"."channel_id" in ('FAKE-hiwOo5')
         */
        return $this->hasOneThrough(
            Plan::class, // the model we want
            Subscription::class, // the intermediary model
            'channel_id', // channel foreign key in subscription
            'id', // plans primary key
            'channel_id', // primary key of channels (THIS ONE IS PROBLEMATIC AGAIN)
            'plan_id' // plan foreign key in subscriptions
        );
    }
}
