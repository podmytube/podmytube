<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ChannelRegistered;
use App\Events\ChannelUpdated;
use App\Events\MediaUploadedByUser;
use App\Events\PodcastUpdated;
use App\Events\ThumbUpdated;
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
        ChannelRegistered::class => [
            UploadPodcastListener::class,
            SendChannelIsRegisteredEmail::class,
        ],
        ChannelUpdated::class => [
            UploadPodcastListener::class,
        ],
        MediaUploadedByUser::class => [
            UploadMediaListener::class,
            UploadPodcastListener::class,
        ],
        ThumbUpdated::class => [
            UploadThumbListener::class,
            UploadPodcastListener::class,
        ],
        PodcastUpdated::class => [
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
