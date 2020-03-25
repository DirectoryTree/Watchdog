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
        \DirectoryTree\Watchdog\Dogs\WatchLogins::class,
        \DirectoryTree\Watchdog\Dogs\WatchPasswordChanges::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Frequency
    |--------------------------------------------------------------------------
    |
    | This option controls how frequently each model can be scanned using
    | the watchdog:monitor command in minutes. Set this to zero to allow
    | scans to be run every time on demand without any limitation.
    |
    */

    'frequency' => 15,

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | The global defaults for all notifications that are sent by watchdogs.
    |
    */

    'notifications' => [

        'mail' => [
            'to' => ['your@email.com'],
        ],

        'date_format' => 'F j @ g:i A',

    ],

    /*
    |--------------------------------------------------------------------------
    | Ignore
    |--------------------------------------------------------------------------
    |
    | The LDAP attributes that should be ignored when detecting object changes.
    |
    | Sensible Active Directory defaults are set here.
    |
    */

    'ignore' => [
        'dscorepropagationdata',
    ],

];
