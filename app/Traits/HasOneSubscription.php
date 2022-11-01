<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Subscription;

trait HasOneSubscription
{
    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'channel_id');
    }

    public function hasSubscription(): bool
    {
        return $this->subscription !== null;
    }
    
}
