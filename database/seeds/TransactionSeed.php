<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transactions')->insert([
            'id' => 100,
            'value' => 100.00,
            'status' => 'PAGO',
            'payment_voucher' => 'www.comprovante.com',
            'donations_id' => 200
        ]);

        DB::table('transactions')->insert([
            'id' => 101,
            'value' => 200.00,
            'status' => 'ABERTO',
            'payment_voucher' => 'www.comprovante2.com',
            'donations_id' => 201
        ]);
    }
}
