<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ChannelRegisteredEvent;
use App\Events\ChannelUpdatedEvent;
use App\Events\MediaUploadedByUserEvent;
use App\Events\PodcastUpdatedEvent;
use App\Events\ThumbUpdatedEvent;
use App\Listeners\SendChannelIsRegisteredEmail;
use App\Listeners\UploadMediaListener;
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
        ChannelRegisteredEvent::class => [
            UploadPodcastListener::class,
            SendChannelIsRegisteredEmail::class,
        ],
        ChannelUpdatedEvent::class => [
            UploadPodcastListener::class,
        ],
        MediaUploadedByUserEvent::class => [
            UploadMediaListener::class,
            UploadPodcastListener::class,
        ],
        ThumbUpdatedEvent::class => [
            UploadThumbListener::class,
            UploadPodcastListener::class,
        ],
        PodcastUpdatedEvent::class => [
            UploadPodcastListener::class,
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
