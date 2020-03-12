<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use DirectoryTree\Watchdog\LdapConnection;
use Faker\Generator as Faker;

$factory->define(LdapConnection::class, function (Faker $faker) {
    return [
        'name' => $faker->domainName,
        'slug' => $faker->slug,
        'model' => LdapRecord\Models\Entry::class,
    ];
});
