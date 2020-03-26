<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use DirectoryTree\Watchdog\LdapConnection;

$factory->define(LdapConnection::class, function (Faker $faker) {
    return [
        'name'  => $faker->domainName,
        'slug'  => $faker->slug,
        'model' => LdapRecord\Models\Entry::class,
    ];
});
