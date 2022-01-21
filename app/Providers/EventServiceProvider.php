<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\UploadPodcastListener;
use App\Listeners\UploadThumbListener;
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
            \App\Listeners\UploadPodcastListener::class,
            \App\Listeners\SendChannelIsRegisteredEmail::class,
        ],
        \App\Events\ChannelUpdated::class => [
            \App\Listeners\UploadPodcastListener::class,
        ],
        \App\Events\MediaUploadedByUser::class => [
            \App\Listeners\UploadMediaListener::class,
            \App\Listeners\UploadPodcastListener::class,
        ],
        // thumb has been updated
        \App\Events\ThumbUpdated::class => [
            UploadThumbListener::class,
            UploadPodcastListener::class,
        ],
        // feed has been updated
        \App\Events\PodcastUpdated::class => [
            \App\Listeners\UploadPodcastListener::class,
        ],
    ];

    protected $subscribe = [];
}
