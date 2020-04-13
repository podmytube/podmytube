<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class ApiKey extends Model
{
    public const PROD_ENV = 1;
    public const LOCAL_ENV = 2;

    protected function getApiKey()
    {
        return $this->environment()
            ->inRandomOrder()
            ->first();
    }

    public function scopeEnvironment(Builder $query)
    {
        return $query->where('environment', '=', $this->environment);
    }

    public function getOne()
    {
        switch (Config::get('APP_ENV')) {
            case 'local':
            case 'testing':
            case 'test':
                $this->environment = self::LOCAL_ENV;
                break;
            default:
                $this->environment = self::PROD_ENV;
        }
        return $this->getApiKey();
    }
}
