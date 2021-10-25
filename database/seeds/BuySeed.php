<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('buys')->insert([
            'id' => 200,
            'description' => 'Compras higiênicas',
            'value' => 100.00,
            'purchase_voucher' => 'www.comprovantecompra.com',
            'users_id' => 300,
            'institutions_id' => 100
        ]);

        DB::table('buys')->insert([
            'id' => 201,
            'description' => 'Compras alimentares',
            'value' => 200.00,
            'purchase_voucher' => 'www.comprovantecompra2.com',
            'users_id' => 300,
            'institutions_id' => 101
        ]);
    }
}
