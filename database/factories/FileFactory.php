<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\File;
use App\Model;
use Faker\Generator as Faker;

$factory->define(File::class, function (Faker $faker) {
    return [
        "url" => $faker->url,
    ];
});
