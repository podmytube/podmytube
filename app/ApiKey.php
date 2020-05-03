<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class ApiKey extends Model
{
    public const PROD_ENV = 1;
    public const LOCAL_ENV = 2;

    /**
     * define the relationship between an apikey and its quotas used.
     */
    public function quotas()
    {
        return $this->hasMany(Quota::class, 'apikey_id');
    }

    public function getOne()
    {
        return $this->usableKeysForToday()->first()->apikey;
    }

    public function scopeEnvironment(Builder $query)
    {
        switch (config('app.env')) {
            case 'local':
            case 'testing':
            case 'test':
                $environment = self::LOCAL_ENV;
                break;
            default:
                $environment = self::PROD_ENV;
        }
        return $query->where('environment', '=', $environment);
    }

    protected static function usableKeysForToday()
    {
        // getting keys according to current env
        return self::environment()
            ->get()
            // calc sum of quota used for this key on today
            ->map(function (ApiKey $apikey) {
                $apikey->quotaUsed = 0;
                if ($apikey->quotas->count()) {
                    $apikey->quotaUsed = $apikey->quotas
                        ->whereBetween('created_at', [
                            Carbon::today(),
                            Carbon::now(),
                        ])
                        ->sum('quota_used');
                }
                return $apikey;
            })
            // removing the one that have already passed the limit
            ->filter(function ($apikey) {
                return $apikey->quotaUsed < Quota::LIMIT_PER_DAY;
            })
            // ordering by quota used asc
            ->sortBy('quotaUsed');
    }
}
