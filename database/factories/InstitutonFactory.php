<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Institution;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Institution::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'code' => random_int(1,1000),
        'logo' => $faker->imageUrl($width = 640, $height = 480),
        'cnpj' => $faker->cnpj,
        'email' => $faker->unique()->safeEmail,
        'phone_number' =>  $faker->phoneNumber,
        'redirect_link' => $faker->url
    ];
});
