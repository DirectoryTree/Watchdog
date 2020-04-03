<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapWatcher;

$factory->define(LdapScan::class, function (Faker $faker) {
    return [
        'watcher_id' => function () {
            return factory(LdapWatcher::class)->create()->id;
        },
    ];
});
