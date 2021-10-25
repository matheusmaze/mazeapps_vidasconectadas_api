<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

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
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $faker->password,
        'document' => $faker->cpf,
        'birthday' =>  $faker->date($format = 'Y-m-d', $max = 'now'),
        'phone_number' => $faker->phoneNumber,
        'nivel' => random_int(1,10),
        'blood_donator' => (bool)random_int(0,1),
    ];
});
