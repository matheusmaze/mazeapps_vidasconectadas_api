<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 100,
            'name' => 'Adm Master',
            'email' => 'adm@email.com',
            'password' => bcrypt('123123'),
            'document' => '12345678901',
            'birthday' => '1999-06-05',
            'phone_number' => '19999033652',
            'nivel' => 'MASTER',
            'blood_donator' => 1,
        ]);

        DB::table('users')->insert([
            'id' => 200,
            'name' => 'Negociador',
            'email' => 'negociador@email.com',
            'password' => bcrypt('123123'),
            'document' => '1234512345',
            'birthday' => '1989-02-03',
            'phone_number' => '15996950331',
            'nivel' => 'NEGOCIADOR',
            'blood_donator' => 0,
        ]);

        DB::table('users')->insert([
            'id' => 300,
            'name' => 'Padrinho',
            'email' => 'padrinho@email.com',
            'password' => bcrypt('123123'),
            'document' => '98765987659',
            'birthday' => '1989-02-03',
            'phone_number' => '15996950332',
            'nivel' => 'PADRINHO',
            'blood_donator' => 0,
        ]);

    }
}
