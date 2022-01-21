<?php

declare(strict_types=1);

namespace App\Providers;

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
            \App\Listeners\UploadPodcast::class,
            \App\Listeners\SendChannelIsRegisteredEmail::class,
        ],
        \App\Events\ChannelUpdated::class => [
            \App\Listeners\UploadPodcast::class,
        ],
        \App\Events\MediaUploadedByUser::class => [
            \App\Listeners\UploadMedia::class,
            \App\Listeners\UploadPodcast::class,
        ],
        // thumb has been updated
        \App\Events\ThumbUpdated::class => [
            UploadThumbListener::class,
            \App\Listeners\UploadPodcast::class,
        ],
        // feed has been updated
        \App\Events\PodcastUpdated::class => [
            \App\Listeners\UploadPodcast::class,
        ],
    ];

    protected $subscribe = [];
}
