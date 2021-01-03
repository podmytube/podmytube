<?php

namespace App\Traits;

use App\Subscription;

trait HasOneSubscription
{
    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'channel_id');
    }
}
