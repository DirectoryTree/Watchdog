<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Here you may specify which LdapRecord models you would like to be monitored.
    | You must import the below monitored models via the watchdog:setup command.
    |
    */

    'models' => [
        LdapRecord\Models\ActiveDirectory\Entry::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Watchdogs
    |--------------------------------------------------------------------------
    |
    | The watchdogs to execute when changes are detected on LDAP objects.
    |
    */

    'watchdogs' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Frequency
    |--------------------------------------------------------------------------
    |
    | This option controls how frequently each model is scanned using
    | the watchdog:monitor command in minutes.
    |
    */

    'frequency' => 15,

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'notifications' => [

        'mail' => [
            'to' => ['your@email.com'],
        ],

        'slack' => [
            'webhook_url' => env('WATCHDOG_SLACK_WEBHOOK_URL'),
        ],

        'notifiable' => \DirectoryTree\WatchDog\Notifications\Notifiable::class,

        /*
         * The date format used in notifications.
         */
        'date_format' => 'Y-m-d h:i:s A',

    ],

    /*
    |--------------------------------------------------------------------------
    | Ignore
    |--------------------------------------------------------------------------
    |
    | The LDAP attributes that should be ignored when detecting object changes.
    |
    */

    'ignore' => [
        //
    ],

];
