<?php

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

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        /**
         * Thumbs from the dashboard
         */
        'thumbs' => [
            'driver' => 'local',
            'root' => storage_path('app/public/thumbs'),
            'url' => env('APP_URL') . '/storage/thumbs',
            'visibility' => 'public',
            'cache' => [
                'store' => 'memcached',
                'expire' => 600,
                'prefix' => 'thumbs-prefix',
            ],
        ],

        /**
         * Thumbs real url for podcast listeners
         */
        'sftpthumbs' => [
            'driver' => 'sftp',
            'host' => 'ns3309553.ip-5-135-160.eu',
            'username' => 'fred',
            //'password' => 'your-password',

            // Settings for SSH key based authentication...
            'privateKey' => './.ssh/SFTPthumb',
            // 'password' => 'encryption-password',

            // Optional SFTP Settings...
            // 'port' => 22,
            'root' => env('SFTP_THUMBS_PATH'),
            // 'timeout' => 30,
            'url' => env('THUMB_URL'),
            'cache' => [
                'store' => 'memcached',
                'expire' => 600,
                'prefix' => 'thumbs-prefix',
            ],
        ],

        'old_thumbs' => [
            'driver' => 'local',
            'root'   => '../www/thumbs',
        ],

        'appTmp' => [
            'driver' => 'local',
            'root' => base_path('tmp'),
        ],

    ],

];
