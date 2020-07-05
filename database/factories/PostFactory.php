<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'title' => $faker->realText($faker->numberBetween(50, 128)),
        'content' => $faker->realText(1000),
        'type' => $faker->numberBetween(0, 2),
    ];
});
