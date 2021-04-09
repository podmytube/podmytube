<?php

namespace App;

use App\Traits\BelongsToPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StripePlan extends Model
{
    use BelongsToPlan;

    public $timestamps = false;
    public $casts = [
        'is_yearly' => 'boolean',
    ];

    public function scopeIsYearly(Builder $query)
    {
        return $query->where('is_yearly', '=', true);
    }
}
