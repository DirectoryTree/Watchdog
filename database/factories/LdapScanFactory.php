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

// LdapScan state definitions.
$factory->state(LdapScan::class, LdapScan::STATE_CREATED, [
    'state' => LdapScan::STATE_CREATED,
]);

$factory->state(LdapScan::class, LdapScan::STATE_IMPORTING, function () {
    return [
        'state' => LdapScan::STATE_IMPORTING,
        'started_at' => now(),
    ];
});

$factory->state(LdapScan::class, LdapScan::STATE_IMPORTED, function (Faker $faker) {
    return [
        'state' => LdapScan::STATE_IMPORTED,
        'imported' => $faker->numberBetween(0, 10),
    ];
});

$factory->state(LdapScan::class, LdapScan::STATE_PROCESSING, [
    'state' => LdapScan::STATE_PROCESSING,
]);

$factory->state(LdapScan::class, LdapScan::STATE_PROCESSED, [
    'state' => LdapScan::STATE_PROCESSED,
]);

$factory->afterMakingState(LdapScan::class, LdapScan::STATE_PROCESSED, function (LdapScan $scan) {
    $scan->processed = $scan->imported;
});

$factory->state(LdapScan::class, LdapScan::STATE_DELETING_MISSING, [
    'state' => LdapScan::STATE_DELETING_MISSING,
]);

$factory->state(LdapScan::class, LdapScan::STATE_DELETED_MISSING, [
    'state' => LdapScan::STATE_DELETED_MISSING,
]);

$factory->state(LdapScan::class, LdapScan::STATE_PURGING, [
    'state' => LdapScan::STATE_PURGING,
]);

$factory->state(LdapScan::class, LdapScan::STATE_PURGED, function () {
    return [
        'state' => LdapScan::STATE_PURGED,
        'completed_at' => now(),
    ];
});
