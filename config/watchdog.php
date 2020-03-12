<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Models Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify which LdapRecord models you would like to be monitored.
    | You must import the below monitored models via the watchdog:setup command,
    | otherwise they will not be monitored.
    |
    */

    'models' => [
        LdapRecord\Models\ActiveDirectory\Entry::class,
    ],

    'frequency' => 15,

    'notifiers' => [
        //
    ],

];
