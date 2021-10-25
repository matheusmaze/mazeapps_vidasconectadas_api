<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Buy;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Buy::class, function (Faker $faker) {
    return [
        "description" => $faker->text,
        "value" => random_int(1,200),
        "purchase_voucher" => Str::random(100),
    ];
});
