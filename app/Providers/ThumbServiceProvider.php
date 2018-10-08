<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\ThumbService;

class ThumbServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Debugbar::addMessage('hello from service provider');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        var_dump("hello");
        $this->app->singleton('ThumbService', function($app) {
            return new ThumbService();
        });
    }
}
