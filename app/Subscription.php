<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /**
     * One channel should have only one row in subscription table.
     */
    public function channel()
    {
        return $this->hasOne(Channel::class);
    }

    /**
     * One subscription has only one plan possible.
     * The belongTo means that subscription table has the plan_id foreign key within.
     * @return object App\Plan
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    
}
