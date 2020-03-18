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
    | Frequency
    |--------------------------------------------------------------------------
    |
    | This option controls how frequently each model is scanned using
    | the watchdog:feed command in minutes.
    |
    */

    'frequency' => 15,

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

];
