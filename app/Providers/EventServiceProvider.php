<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ChannelRegistered;
use App\Events\ChannelUpdated;
use App\Events\MediaUploadedByUser;
use App\Events\PodcastUpdated;
use App\Events\ThumbUpdated;
use App\Listeners\UploadPodcastListener;
use App\Listeners\UploadThumbListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ChannelRegistered::class => [
            \App\Listeners\UploadPodcastListener::class,
            \App\Listeners\SendChannelIsRegisteredEmail::class,
        ],
        ChannelUpdated::class => [
            \App\Listeners\UploadPodcastListener::class,
        ],
        MediaUploadedByUser::class => [
            \App\Listeners\UploadMediaListener::class,
            \App\Listeners\UploadPodcastListener::class,
        ],
        ThumbUpdated::class => [
            UploadThumbListener::class,
            UploadPodcastListener::class,
        ],
        PodcastUpdated::class => [
            \App\Listeners\UploadPodcastListener::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Verified::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    protected $subscribe = [];
}
