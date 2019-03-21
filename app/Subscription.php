<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'channel_id',
        'plan_id',
    ];
    /**
     * One channel should have only one row in subscription table.
     * Setting local_key and foreign key are required because of the channel_id primary key. 
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'channel_id');
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
