<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Watch
    |--------------------------------------------------------------------------
    |
    | Here you may specify which LdapRecord models you would like to be watched.
    |
    | You must import the below monitored models via the watchdog:setup command.
    |
    */

    'watch' => [
        \LdapRecord\Models\ActiveDirectory\Entry::class => [
            \DirectoryTree\Watchdog\Dogs\WatchGroupMembers::class    => ['mail'],
            \DirectoryTree\Watchdog\Dogs\WatchComputerLogons::class  => ['mail'],
            \DirectoryTree\Watchdog\Dogs\WatchAccountGroups::class   => ['mail'],
            \DirectoryTree\Watchdog\Dogs\WatchAccountEnable::class   => ['mail'],
            \DirectoryTree\Watchdog\Dogs\WatchAccountLogons::class   => ['mail'],
            \DirectoryTree\Watchdog\Dogs\WatchAccountDisable::class  => ['mail'],
            \DirectoryTree\Watchdog\Dogs\WatchAccountLockout::class  => ['mail'],
            \DirectoryTree\Watchdog\Dogs\WatchPasswordChanges::class => ['mail'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Frequency (minutes)
    |--------------------------------------------------------------------------
    |
    | This option controls how frequently each model can be scanned using
    | the watchdog:monitor command in minutes. Set this to zero to allow
    | scans to be run every time on demand without any limitation.
    |
    */

    'frequency' => 5,

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | The global defaults for all notifications that are sent by watchdogs.
    |
    | The date format is used when outputting all dates in default notifications.
    |
    */

    'notifications' => [

        'mail' => [
            'to' => [env('WATCHDOG_NOTIFICATION_EMAIL', 'your@email.com')],
        ],

        // ex. January 1st, 2020 @ 00:00 AM
        'date_format' => 'F jS, Y @ g:i A',

        // These seconds are applied to dispatched notification jobs you
        // don't receive the "Too many emails per second" SMTP error.
        'seconds_between_notifications' => 5,

    ],

    'attributes' => [

        /*
        |--------------------------------------------------------------------------
        | Attribute Transformers
        |--------------------------------------------------------------------------
        |
        | The LDAP attributes that should be transformed into their given types.
        | Modify the transformers below to change how they are transformed.
        |
        */

        'transform' => [
            'objectsid'             => 'objectsid',
            'whenchanged'           => 'windows',
            'whencreated'           => 'windows',
            'dscorepropagationdata' => 'windows',
            'lastlogon'             => 'windows-int',
            'lastlogontimestamp'    => 'windows-int',
            'pwdlastset'            => 'windows-int',
            'lockouttime'           => 'windows-int',
            'accountexpires'        => 'windows-int',
            'badpasswordtime'       => 'windows-int',
        ],

        'transformers' => [
            'objectsid'   => \DirectoryTree\Watchdog\Ldap\Transformers\ObjectSid::class,
            'windows'     => \DirectoryTree\Watchdog\Ldap\Transformers\WindowsTimestamp::class,
            'windows-int' => \DirectoryTree\Watchdog\Ldap\Transformers\WindowsIntTimestamp::class,
        ],

        /*
        |--------------------------------------------------------------------------
        | Attributes to Ignore
        |--------------------------------------------------------------------------
        |
        | The LDAP attributes that should be ignored when detecting object changes.
        |
        | These attributes will not create change records on LDAP objects.
        |
        | Sensible Active Directory defaults are set here.
        |
        */

        'ignore' => [
            'dscorepropagationdata',
        ],

    ],

];
