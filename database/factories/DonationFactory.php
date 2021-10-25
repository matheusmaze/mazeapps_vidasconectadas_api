<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Donation;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Donation::class, function (Faker $faker) {
    $date = mt_rand(19200101,20211231);
    return [
        'recurrence_day' =>   $faker->date($format = 'Y-m-d', $max = 'now'),
        'recurrence_interval' => random_int(1,4),
        'end_recurrence' => date('Y-m-d', $date),
        'notification_type' => random_int(1,2),
        'payment_form' => random_int(1,3),
        'fixed_value' => random_int(1,200),
    ];
});
