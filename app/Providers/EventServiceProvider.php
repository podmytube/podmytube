<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\ChannelRegistered::class => [
            \App\Listeners\RefreshPodcast::class,
            \App\Listeners\SendChannelIsRegisteredEmail::class,
        ],
        \App\Events\ChannelUpdated::class => [
            \App\Listeners\RefreshPodcast::class,
        ],
        \App\Events\MediaRegistered::class => [
            \App\Listeners\QuotaReachedWarning::class,
        ],
        \App\Events\ThumbUpdated::class => [
            \App\Listeners\UploadThumb::class,
            \App\Listeners\RefreshPodcast::class,
            \App\Listeners\RefreshVignette::class,
        ],
    ];

    /**
     * this RefreshPodcast listener will listen to
     * ChannelRegistered, ChannelUpdated and ThumbUpdated
     */
    protected $subscribe = [
        /* 'App\Listeners\RefreshPodcast', */
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
