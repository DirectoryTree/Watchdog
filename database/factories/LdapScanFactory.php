<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapWatcher;
use DirectoryTree\Watchdog\LdapScanEntry;

$factory->define(LdapScan::class, function (Faker $faker) {
    return [
        'watcher_id' => function () {
            return factory(LdapWatcher::class)->create()->id;
        },
    ];
});

$factory->define(LdapScanEntry::class, function (Faker $faker) {
    return [
        'scan_id' => function () {
            return factory(LdapScan::class)->create()->id;
        },
        'guid'            => $faker->uuid,
        'type'            => 'user',
        'values'          => [],
        'ldap_updated_at' => now(),
    ];
});

$factory->afterMaking(LdapScanEntry::class, function (LdapScanEntry $entry, Faker $faker) {
    $entry->name = $faker->name;
    $entry->dn = "cn={$entry->name},dc=local,dc=com";
});
