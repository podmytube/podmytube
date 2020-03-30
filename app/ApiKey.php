<?php

namespace App;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ApiKey extends Model
{
    public const _PROD_ENV = 1;
    public const _LOCAL_ENV = 2;

    protected function setEnvironment(int $environment = self::_PROD_ENV)
    {
        $this->environment = $environment;
    }

    public function developmentKeys()
    {
        $this->setEnvironment(self::_LOCAL_ENV);
        return $this->environment()
            ->get()
            ->pluck('apikey');
    }

    public function productionKeys()
    {
        $this->setEnvironment(self::_PROD_ENV);
        return $this->environment()
            ->get()
            ->pluck('apikey');
    }

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
        switch (getenv('APP_ENV')) {
            case 'local':
            case 'testing':
            case 'test':
                $this->setEnvironment(self::_LOCAL_ENV);
                break;
            default:
                $this->setEnvironment(self::_PROD_ENV);
        }
        return $this->getApiKey();
    }
}
