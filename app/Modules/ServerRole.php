<?php

declare(strict_types=1);

namespace App\Modules;

use Illuminate\Support\Facades\Config;

class ServerRole
{
    public const DISPLAY = 0;
    public const WORKER = 1;
    public const HOSTING = 2;

    public const DEFAULT = self::DISPLAY;

    protected static $rolesMap = [
        'display' => self::DISPLAY,
        'hosting' => self::HOSTING,
        'worker' => self::WORKER,
    ];

    public static function getRole(): int
    {
        if (!array_key_exists(config('app.server_role'), self::$rolesMap)) {
            return self::DEFAULT;
        }

        return self::$rolesMap[Config::get('app.server_role')];
    }

    public static function isWorker(): bool
    {
        if (self::isLocal()) {
            return true;
        }

        return self::$rolesMap[Config::get('app.server_role')] === self::WORKER;
    }

    public static function isLocal(): bool
    {
        return Config::get('app.server_role') === 'local';
    }

    public static function isDisplay(): bool
    {
        if (self::isLocal()) {
            return true;
        }

        return self::$rolesMap[Config::get('app.server_role')] === self::DISPLAY;
    }
}
