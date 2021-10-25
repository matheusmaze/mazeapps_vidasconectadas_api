<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitutionSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('institutions')->insert([
            'id' => 100,
            'name' => 'AACD',
            'code' => '012345',
            'logo' => 'aacd.png',
            'cnpj' => '12345678912345',
            'email' => 'aacd@email.com',
            'phone_number' => '16993311025',
            'redirect_link' => 'www.aacd.com.br'
        ]);

        DB::table('institutions')->insert([
            'id' => 101,
            'name' => 'Criança feliz',
            'code' => '98765',
            'logo' => 'cf.png',
            'cnpj' => '9876543210123',
            'email' => 'cf@email.com',
            'phone_number' => '15986951027',
            'redirect_link' => 'www.cf.com.br'
        ]);
    }
}
