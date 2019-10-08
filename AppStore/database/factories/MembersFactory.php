<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Members;
use Faker\Generator as Faker;

$factory->define(Members::class, function (Faker $faker) {
    return [
        // 'name' => $faker->name,
        // 'phone' => $faker->phoneNumber,
        // 'email' => $faker->safeEmail,
        // 'idNumber' => $faker->bcrypt('secret'),
        // 'password' => $faker->bcrypt('secret'),
        
    ];
});
