<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
namespace Database\Factories;

use App\Profile;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'username' => $faker->userName,
        'email_verified_at' => now(),
        'type' => $faker->randomElement([User::STATUS['ACTIVE'], User::STATUS['BANNED']]),
        'status' => $faker->numberBetween(0, 1),
        'password' => 'secret',
        'remember_token' => Str::random(10),
    ];
});

$factory->state(User::class, 'admin', [
    'type' => User::TYPES['admin'],
]);

$factory->state(User::class, 'user', [
    'type' => User::TYPES['user'],
]);

$factory->state(User::class, 'active', [
    'status' => User::STATUS['ACTIVE'],
]);

$factory->state(User::class, 'banned', [
    'status' => User::STATUS['BANNED'],
]);

$factory->state(User::class, 'deleted', function (Faker $faker) {
    return [
        'deleted_at' => $faker->dateTime
    ];
});


$factory->afterCreating(User::class, function (User $user, $faker) {
    $user->profile()->save(factory(Profile::class)->make());
});
