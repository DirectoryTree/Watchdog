<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapConnection;
use DirectoryTree\Watchdog\LdapScanEntry;
use Faker\Generator as Faker;

$factory->define(LdapScan::class, function (Faker $faker) {
    return array(
        'connection_id' => function () {
            return factory(LdapConnection::class)->create()->id;
        },
    );
});

$factory->define(LdapScanEntry::class, function (Faker $faker) {
    return [
        'scan_id' => function () {
            return factory(LdapScan::class)->create()->id;
        },
        'guid' => $faker->uuid,
        'type' => 'user',
        'values' => [],
        'ldap_updated_at' => now(),
    ];
});

$factory->afterMaking(LdapScanEntry::class, function (LdapScanEntry $entry, Faker $faker) {
    $entry->name = $faker->name;
    $entry->dn = "cn={$entry->name},dc=local,dc=com";
});
