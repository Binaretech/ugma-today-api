<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Advice;
use Faker\Generator as Faker;

$factory->define(Advice::class, function (Faker $faker) {
    return [
        'title' => $faker->realText(128),
        'content' => $faker->realText(500),
        'show_at' => $faker->boolean() ? $faker->dateTime() : null,
        'expire_at' => $faker->boolean() ? $faker->dateTime() : null,
    ];
});
