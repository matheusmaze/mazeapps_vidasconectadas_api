<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStandard extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Matheus Costa Pereira',
            'email' => 'matheus.pereira@mazeapps.com.br',
            'password' => bcrypt('115225'),
            'document' => '11522561650',
            'birthday' => '1991-11-08',
            'phone_number' => '35988568772',
            'nivel' => 'MASTER',
            'blood_donator' => 1,
        ]);
        DB::table('users')->insert([
            'id' => 2,
            'name' => 'Danilo Luís dos Reis Bernardes',
            'email' => 'danilo.bernardes9135@gmail.com',
            'password' => bcrypt('086608'),
            'document' => '08660892682',
            'birthday' => '1999-06-05',
            'phone_number' => '3584453253',
            'nivel' => 'MASTER',
            'blood_donator' => 1,
        ]);
        DB::table('users')->insert([
            'id' => 3,
            'name' => 'Kaynan reais',
            'email' => 'adm@email.com',
            'password' => bcrypt('098526'),
            'document' => '09852674617',
            'birthday' => '1990-04-07',
            'phone_number' => '3592271990',
            'nivel' => 'MASTER',
            'blood_donator' => 1,
        ]);
        DB::table('users')->insert([
            'id' => 4,
            'name' => 'Lucas Henrique da Costa',
            'email' => 'comprador@email.com',
            'password' => bcrypt('088291'),
            'document' => '08829167690',
            'birthday' => '1989-07-01',
            'phone_number' => '3599164799',
            'nivel' => 'NEGOCIADOR',
            'blood_donator' => 1,
        ]);
        DB::table('users')->insert([
            'id' => 5,
            'name' => 'Edgard Claudiano',
            'email' => 'edgarclaudiano@hotmail.com',
            'password' => bcrypt('051486'),
            'document' => '05148636688',
            'birthday' => '1989-07-01',
            'phone_number' => '3592441906',
            'nivel' => 'NEGOCIADOR',
            'blood_donator' => 1,
        ]);
        DB::table('users')->insert([
            'id' => 6,
            'name' => 'Fernando',
            'email' => 'nfernandoateixeira@gmail.com',
            'password' => bcrypt('084573'),
            'document' => '08457307622',
            'birthday' => '1989-07-01',
            'phone_number' => '35988180887',
            'nivel' => 'NEGOCIADOR',
            'blood_donator' => 1,
        ]);
    }
}
