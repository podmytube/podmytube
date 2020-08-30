<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\App;

trait HasDomain
{
    protected $domain;

    protected function setDomain($domain)
    {
        if (!isset($domain)) {
            $domain = env('APP_DOMAIN');
        }
        $this->domain = $domain;
    }

    protected function getDomain()
    {
        $protocol = 'http://';
        if (App::environment('production')) {
            $protocol = 'https://';
        }
        return $protocol . $this->domain;
    }
}
