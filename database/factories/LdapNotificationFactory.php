<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapNotification;
use DirectoryTree\Watchdog\Notifications\ObjectHasChanged;

$factory->define(LdapNotification::class, function (Faker $faker) {
    return [
        'object_id' => function () {
            return factory(LdapObject::class)->create()->id;
        },
        'watchdog'     => app(Watchdog::class)->getKey(),
        'notification' => ObjectHasChanged::class,
        'data'         => [],
        'sent'         => false,
        'channels'     => [],
    ];
});
