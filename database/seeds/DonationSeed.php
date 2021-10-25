<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonationSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('donations')->insert([
            'id' => 200,
            'recurrence_day' => '2021-06-22',
            'recurrence_interval' => 'MENSAL',
            'end_recurrence' => '2021-08-22',
            'notification_type' => 'EMAIL',
            'payment_type' => 'PIX',
            'fixed_value' => 100.00,
            'users_id' => 300
        ]);

        DB::table('donations')->insert([
            'id' => 201,
            'recurrence_day' => '2021-06-18',
            'recurrence_interval' => 'UNICA',
            'end_recurrence' => '2021-06-18',
            'notification_type' => 'WHATSAPP',
            'payment_type' => 'CARTAO',
            'fixed_value' => 250.00,
            'users_id' => 300
        ]);
    }
}
