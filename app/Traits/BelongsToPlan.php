<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Plan;

trait BelongsToPlan
{
    /**
     * define the relationship between media and its channel.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }
}
