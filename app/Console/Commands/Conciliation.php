<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Transaction;
use App\Donation;
use App\Log;
use App\PaymentGateways\MazepagGateway;

class Conciliation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routine:conciliation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates donations status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transactions = Transaction::where('status', 'PENDING')
                                    ->whereNotNull('txid')
                                    ->select('id', 'txid')
                                    ->get();

        if(!isset($transactions[0])) {

            Log::create([
                'controller' => 'Conciliation Routine',
                'uri' => 'App\Console\Commands\Conciliation',
                'body'=> 'Nenhuma Transferência em aberto'
            ]);

            return 'Nenhuma transferência em aberto';
        }

        $mazepag = new MazepagGateway();
        Log::create([
            'controller' => 'Nenhuma Transferência em aberto',
            'uri' => 'URI QUALQUER',
            'body'=> json_encode($transactions)
        ]);
        $res = $mazepag->consult_array($transactions);

        $count['Paid'] = 0;
        $count['Expired'] = 0;
        $count['Pending'] = 0;

        foreach ($res as $r) {

            if (isset($r->status)) {
                $transaction = Transaction::find($r->id);
                $transaction->status = $r->status;

                switch ($r->status) {
                    case 'EXPIRED':
                        $donation = Donation::where('id', $transaction->donations_id)->first();
                        $donation->status = 'OVERDUE';
                        $donation->save();

                        $count['Expired'] += 1;
                        break;
                    case 'PAID':
                        $transaction->payment_date = $r->data_pagamento;

                        $donation = Donation::where('id', $transaction->donations_id)->first();
                        $donation->status = 'ACTIVE';
                        $donation->save();

                        $count['Paid'] += 1;
                        break;
                }

                $transaction->save();
            } else {
                $count['Pending'] += 1;
            }
        }

        Log::create([
            'controller' => 'Conciliation Routine',
            'uri' => 'App\Console\Commands\Conciliation',
            'body'=> json_encode($count)
        ]);

    }
}
