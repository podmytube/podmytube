<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quota extends Model
{
    public const LIMIT_PER_DAY = 10000;
    public const START_HOUR = '08:00:00'; // UTC pacific time
    public const END_HOUR = '07:59:59'; // UTC pacific time

    protected $fillable = [
        'apikey_id',
        'script',
        'quota_used',
        'created_at',
    ];

    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class, 'apikey_id', 'id');
    }
}
