<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3-backup' => [
            'driver' => 's3',
            'key' => env('S3_BACKUP_ACCESS_KEY_ID'),
            'secret' => env('S3_BACKUP_SECRET_ACCESS_KEY'),
            'region' => env('S3_BACKUP_REGION'),
            'bucket' => env('S3_BACKUP_BUCKET'),
            'url' => env('S3_BACKUP_URL'),
            'endpoint' => env('S3_BACKUP_ENDPOINT'),
            'use_path_style_endpoint' => env('S3_BACKUP_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'root' => env('S3_BACKUP_ROOT'),
        ],

        'backup' => [
            'driver' => 'ftp',
            'host' => env('BACKUP_HOST'),
            'username' => env('BACKUP_USERNAME'),
            'password' => env('BACKUP_PASSWORD'),
            'port' => (int) env('BACKUP_PORT', 21),
            'root' => env('BACKUP_ROOT'),
            'passive' => true,
            'ssl' => true,
            // 'timeout' => 30,
        ],

        'temp' => [
            'driver' => 'local',
            'root' => storage_path('app/temp'),
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
