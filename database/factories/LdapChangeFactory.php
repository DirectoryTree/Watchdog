<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use DirectoryTree\Watchdog\LdapChange;
use DirectoryTree\Watchdog\LdapObject;
use Faker\Generator as Faker;

$factory->define(LdapChange::class, function (Faker $faker) {
    return [
        'object_id' => function () {
            return factory(LdapObject::class)->create()->id;
        },
        'ldap_updated_at' => now(),
        'attribute' => $faker->word,
        'before' => [$faker->word],
        'after' => [$faker->word],
    ];
});
