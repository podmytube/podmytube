<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public function subscriptions () 
    {
        return $this->HasMany(Subscription::class);
    } 
}
