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
     */
    public function plan()
    {
        return $this->hasOne(Plan::class);
    }
    
}
