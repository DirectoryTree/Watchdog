<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use LdapRecord\Models\Entry;
use Faker\Generator as Faker;

$factory->define(Entry::class, function (Faker $faker) {
    return [
        'objectguid' => [$faker->uuid],
        'cn'         => [$faker->name],
    ];
});
