<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Transaction;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'value' => random_int(1,200),
        'status' => random_int(1,2),
        'checking_copy' => Str::random(100)
    ];
});
