<?php

namespace App\Traits;

use App\Plan;

trait BelongsToPlan
{
    /**
     * define the relationship between media and its channel
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }
}
