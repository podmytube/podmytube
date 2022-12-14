<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'sessions' => [
            'driver' => 'local',
            'root' => base_path('storage/framework/sessions'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'newsletter' => [
            'driver' => 'local',
            'root' => base_path('resources/views/emails/newsletters/'),
        ],

        // Local podcast feeds
        'feeds' => [
            'driver' => 'local',
            'root' => storage_path('app/public/feeds'),
            'url' => env('APP_URL') . '/storage/feeds',
            'visibility' => 'public',
        ],

        'thumbs' => [
            'driver' => 'local',
            'root' => storage_path('app/public/thumbs'),
            'url' => env('THUMBS_URL', 'https://thumbs.podmytube.com'),
            'visibility' => 'public',
        ],

        'vignettes' => [
            'driver' => 'local',
            'root' => storage_path('app/public/vignettes'),
            'url' => env('APP_URL') . '/storage/vignettes',
            'visibility' => 'public',
        ],

        // Medias
        'medias' => [
            'driver' => 'sftp',
            'host' => config('app.sftp_host'),
            'username' => config('app.sftp_user'),
            'privateKey' => config('app.sftp_key_path'),
            // 'port' => 22,
            'root' => env('SFTP_MP3_PATH'),
            'url' => env('MP3_URL'),
            'timeout' => 20,
            'visibility' => 'public',
            'permPublic' => 0755,
        ],

        // Uploaded medias
        'uploadedMedias' => [
            'driver' => 'local',
            'root' => storage_path('app/uploadedMedias'),
            'visibility' => 'private',
        ],

        'remote' => [
            'driver' => 'sftp',
            'host' => config('app.sftp_host'),
            'username' => config('app.sftp_user'),
            'privateKey' => config('app.sftp_key_path'),
            'root' => config('app.sftp_root'),
            'timeout' => 30,
            'visibility' => 'public',
            'permPublic' => 0644,
            'directoryPerm' => 0755,
            'throw' => true,
        ],

        'tmp' => [
            'driver' => 'local',
            'root' => base_path('tmp'),
            'visibility' => 'public',
            'permPublic' => 0644,
            'directoryPerm' => 0755,
        ],
    ],
];
