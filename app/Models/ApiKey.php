<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\YoutubeNoApiKeyAvailableException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiKey extends Model
{
    use HasFactory;

    public const PROD_ENV = 1;
    public const LOCAL_ENV = 2;

    /**
     * define the relationship between an apikey and its quotas used.
     */
    public function quotas()
    {
        return $this->hasMany(Quota::class, 'apikey_id');
    }

    /**
     * return one api key.
     *
     * @return string $apikey the api key to use
     */
    public static function getOne(): string
    {
        // get all api keys
        // get all api keys consumption
        // order by consumptions
        // filtering depleted ones

        $apikeys = self::query()
            ->active()
            ->with(['quotas' => function ($query): void {
                $query->select(DB::raw('apikey_id, sum(quotas.quota_used) as consumed'))
                    ->whereBetween('created_at', [today(), now()])
                    ->groupBy('apikey_id')
                ;
            }])
            ->get()
            ->filter(function (ApiKey $apikey): bool {
                // no quota recored for this apikey yet => it is good
                if (!$apikey->quotas->count()) {
                    return true;
                }

                return $apikey->quotas->first()->consumed < Quota::LIMIT_PER_DAY;
            })
            ->map(function (ApiKey $apikey): Apikey {
                if ($apikey->quotas->count()) {
                    $apikey->consumed = $apikey->quotas->first()->consumed;
                } else {
                    $apikey->consumed = 0;
                }

                return $apikey;
            })
            ->sortBy('consumed')
        ;

        if (!$apikeys->count()) {
            throw new YoutubeNoApiKeyAvailableException('No remaining apikey available for today.');
        }

        return $apikeys->first()->apikey;
    }

    public static function byApikey(string $apikey): ?self
    {
        return self::where('apikey', '=', $apikey)->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', '=', true);
    }
}
