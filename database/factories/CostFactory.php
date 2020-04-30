<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cost;
use Faker\Generator as Faker;

$factory->define(Cost::class, function (Faker $faker) {
    return [
        'price' => $faker->randomFloat(2),
        'message' => $faker->boolean() ? $faker->realText(128) : null,
    ];
});
