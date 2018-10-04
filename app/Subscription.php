<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /**
     * Define relation for one subscription with one plan.
     * One subscription has only one plan. Else you should create another sub.
     */
    public function plan()
    {
        return $this->hasOne(Plan::class);
    }
}
