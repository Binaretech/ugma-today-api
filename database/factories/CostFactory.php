<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cost;
use App\User;
use Faker\Generator as Faker;
use Laravel\Passport\Passport;

$factory->define(Cost::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'price' => $faker->randomFloat(2),
        'comment' => $faker->boolean() ? $faker->realText(128) : null,
        'currency' => $faker->randomElement([0, 1])
    ];
});

$factory->afterMakingState(Cost::class, 'user', function (Cost $cost, $faker) {
    Passport::actingAs(factory(User::class)->create());
});
