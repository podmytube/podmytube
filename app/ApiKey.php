<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    public const PROD_ENV = 1;
    public const LOCAL_ENV = 2;

    protected function defineEnvironment(int $environment = self::PROD_ENV)
    {
        $this->environment = $environment;
    }

    public function developmentKeys()
    {
        $this->defineEnvironment(self::LOCAL_ENV);
        return $this->environment()
            ->get()
            ->pluck('apikey');
    }

    public function productionKeys()
    {
        $this->defineEnvironment(self::PROD_ENV);
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
                $this->defineEnvironment(self::LOCAL_ENV);
                break;
            default:
                $this->defineEnvironment(self::PROD_ENV);
        }
        return $this->getApiKey();
    }
}
