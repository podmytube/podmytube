<?php

declare(strict_types=1);

namespace App;

use App\Traits\BelongsToPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

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

    public static function stripeIdsOnly(): Collection
    {
        $query = self::query();
        if (App::environment('production')) {
            $query->select('stripe_live_id as stripe_id');
        } else {
            $query->select('stripe_test_id as stripe_id');
        }

        return $query->get()->pluck('stripe_id');
    }
}
