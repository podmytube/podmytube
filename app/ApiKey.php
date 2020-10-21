<?php

namespace App;

use App\Exceptions\YoutubeNoApiKeyAvailableException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiKey extends Model
{
    public const PROD_ENV = 1;
    public const LOCAL_ENV = 2;

    /** @var App\ApiKey $selectedOne selected model*/
    protected $selectedOne;

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
    public static function getOne():string
    {
        /**
         * get all api keys
         */
        $apiKeys = ApiKey::all();

        /**
         * consumed keys for today
         */
        $consumedKeys = ApiKey::select('quotas.apikey_id', 'api_keys.apikey', DB::raw('SUM(quotas.quota_used) as sum_quota_used'))
            ->join('quotas', 'api_keys.id', '=', 'quotas.apikey_id')
            ->whereBetween('quotas.created_at', [Carbon::today(), Carbon::now()])
            ->groupBy('quotas.apikey_id')
            ->having('sum_quota_used', '>', Quota::LIMIT_PER_DAY)
            ->get();

        /**
         * no consumed keys ? return first apikey
         */
        if (!$consumedKeys->count()) {
            return $apiKeys->first()->apikey;
        }

        /**
         * we have consumed keys ? keeping keys that are not consumed
         */
        $consumedKeyIds = $consumedKeys->pluck('apikey_id')->toArray();
        $availableKeys = $apiKeys->filter(function ($apiKeyModel) use ($consumedKeyIds) {
            if (in_array($apiKeyModel->id, $consumedKeyIds)) {
                return false;
            }
            return true;
        });

        if (!$availableKeys->count()) {
            throw new YoutubeNoApiKeyAvailableException('No remaining apikey available for today.');
        }
        return $availableKeys->first()->apikey;
    }

    public static function byApikey(string $apikey): self
    {
        return self::where('apikey', '=', $apikey)->first();
    }
}
