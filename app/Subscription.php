<?php

namespace App;

use App\Traits\BelongsToChannel;
use App\Traits\BelongsToPlan;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use BelongsToChannel, BelongsToPlan;

    protected $fillable = ['channel_id', 'plan_id'];
}
