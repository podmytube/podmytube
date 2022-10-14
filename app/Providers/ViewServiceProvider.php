<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Channel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['partials.footer'], function ($view): void {
            $view->with(
                'activeChannelsCount',
                // caching for one day
                Cache::remember('active_channels_count', '86400', fn () => Channel::active()->count())
            );
        });
    }
}
