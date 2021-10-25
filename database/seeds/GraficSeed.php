<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GraficSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transactions')->insert([
            'id' => 1,
            'value' => 200.00,
            'status' => 'PAID',
            'payment_voucher' => 'www.comprovante2.com',
            'donations_id' => 200,
            'txid' => 'MAZEPAG2021091404443461242',
            'payment_date' => '2021-09-05'
        ]);
        DB::table('transactions')->insert([
            'id' => 2,
            'value' => 100.00,
            'status' => 'PAID',
            'payment_voucher' => 'www.comprovante2.com',
            'donations_id' => 201,
            'txid' => 'MAZEPAG2021091404443461242',
            'payment_date' => '2021-08-05'
        ]);
        DB::table('transactions')->insert([
            'id' => 3,
            'value' => 80.00,
            'status' => 'PAID',
            'payment_voucher' => 'www.comprovante2.com',
            'donations_id' => 201,
            'txid' => 'MAZEPAG2021091404443461242',
            'payment_date' => '2021-05-01'
        ]);
        DB::table('transactions')->insert([
            'id' => 4,
            'value' => 200.00,
            'status' => 'PAID',
            'payment_voucher' => 'www.comprovante2.com',
            'donations_id' => 200,
            'txid' => 'MAZEPAG2021091404443461242',
            'payment_date' => '2021-06-15'
        ]);
        DB::table('transactions')->insert([
            'id' => 5,
            'value' => 25.50,
            'status' => 'PAID',
            'payment_voucher' => 'www.comprovante2.com',
            'donations_id' => 200,
            'txid' => 'MAZEPAG2021091404443461242',
            'payment_date' => '2021-07-15'
        ]);
        DB::table('transactions')->insert([
            'id' => 6,
            'value' => 46.99,
            'status' => 'PAID',
            'payment_voucher' => 'www.comprovante2.com',
            'donations_id' => 201,
            'txid' => 'MAZEPAG2021091404443461242',
            'payment_date' => '2021-03-20'
        ]);
        DB::table('buys')->insert([
            'id' => 1,
            'description' => 'Compras alimentares',
            'value' => 100.00,
            'purchase_voucher' => 'www.comprovantecompra2.com',
            'users_id' => 300,
            'institutions_id' => 101,
            'status' => 'Aprovada',
            'created_at' => '2021-09-17 00:00:00'
        ]);
        DB::table('buys')->insert([
            'id' => 2,
            'description' => 'Compras higiene',
            'value' => 50.00,
            'purchase_voucher' => 'www.comprovantecompra2.com',
            'users_id' => 300,
            'institutions_id' => 100,
            'status' => 'Aprovada',
            'created_at' => '2021-06-17 00:00:00'
        ]);
        DB::table('buys')->insert([
            'id' => 3,
            'description' => 'Compras alimentares e higiene',
            'value' => 80.00,
            'purchase_voucher' => 'www.comprovantecompra2.com',
            'users_id' => 300,
            'institutions_id' => 101,
            'status' => 'Aprovada',
            'created_at' => '2021-07-12 00:00:00'
        ]);
        DB::table('buys')->insert([
            'id' => 4,
            'description' => 'Compras derivadas',
            'value' => 100.00,
            'purchase_voucher' => 'www.comprovantecompra2.com',
            'users_id' => 300,
            'institutions_id' => 100,
            'status' => 'Aprovada',
            'created_at' => '2021-05-25 00:00:00'
        ]);
        DB::table('buys')->insert([
            'id' => 5,
            'description' => 'Pasta de dentes',
            'value' => 75.00,
            'purchase_voucher' => 'www.comprovantecompra2.com',
            'users_id' => 300,
            'institutions_id' => 101,
            'status' => 'Aprovada',
            'created_at' => '2021-04-25 00:00:00'
        ]);
    }
}
