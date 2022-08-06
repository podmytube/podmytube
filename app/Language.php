<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    public static function byCode(string $code): ?self
    {
        return self::where('code', '=', $code)->first();
    }
}
