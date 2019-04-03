<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripePlan extends Model
{
    /**
     * @return object App\Plan
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }
}
