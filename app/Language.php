<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    public static function byCode(string $code): ?self
    {
        return self::where('code', '=', $code)->first();
    }
}
