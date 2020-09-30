<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Quota extends Model
{
    public const LIMIT_PER_DAY = 10000;
    public const START_HOUR = '08:00:00'; // UTC pacific time
    public const END_HOUR = '07:59:59'; // UTC pacific time

    protected $fillable = ['apikey_id', 'script', 'quota_used', 'created_at'];

    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class, 'apikey_id', 'id');
    }

    public static function byScript(string $script)
    {
        return self::where('script', '=', $script)->get();
    }

    public static function saveScriptConsumption(string $scriptName, array $apikeysAndQuotas)
    {
        $dataToInsert = [];
        foreach ($apikeysAndQuotas as $apikey => $quota) {
            $apiKey = ApiKey::byApikey($apikey);
            $dataToInsert[] = [
                'apikey_id' => $apiKey->id,
                'script' => $scriptName,
                'quota_used' => $quota,
                'created_at' => Carbon::now(),
            ];
        }
        Quota::insert($dataToInsert);
    }
}
