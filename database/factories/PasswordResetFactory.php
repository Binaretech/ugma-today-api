<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PasswordReset;
use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(PasswordReset::class, function (Faker $faker) {
    return [
        'token' => base64_encode($faker->realText(12)),
        'user_id' => factory(User::class)->create(),
        'expire_at' => Carbon::now()->addHours(2),
    ];
});
