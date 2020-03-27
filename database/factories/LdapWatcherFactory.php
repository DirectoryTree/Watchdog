<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use DirectoryTree\Watchdog\LdapWatcher;

$factory->define(LdapWatcher::class, function (Faker $faker) {
    return [
        'name'  => $faker->domainName,
        'model' => LdapRecord\Models\Entry::class,
    ];
});
