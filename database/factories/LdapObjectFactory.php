<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapWatcher;

$factory->define(LdapObject::class, function (Faker $faker) {
    return [
        'guid'   => $faker->uuid,
        'type'   => 'user',
        'values' => [],
    ];
});

$factory->afterMaking(LdapObject::class, function (LdapObject $object, Faker $faker) {
    if (!$object->watcher_id) {
        $connection = factory(LdapWatcher::class)->create();
        $object->watcher()->associate($connection);
    }

    $object->name = $faker->name;
    $object->dn = "cn={$object->name},dc=local,dc=com";
});
