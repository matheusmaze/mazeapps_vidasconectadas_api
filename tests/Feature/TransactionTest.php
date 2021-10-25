<?php

namespace Tests\Feature;

use App\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Exception;

class TransactionTest extends TestCase
{
    function __construct()
    {
        parent::setUp();
    }

     /** @test */
     public function createTransaction($id, $value)
     {
         try{
            $transaction = factory(Transaction::class)->make([
                'donations_id' => $id,
                'value' => $value
            ]);

            $this->assertIsInt($transaction['value']);
            $this->assertLessThanOrEqual(2, $transaction['status']);
            $this->assertGreaterThanOrEqual(1, $transaction['status']);
            $this->assertIsString($transaction['checking_copy']);
            $this->assertIsInt($transaction['donations_id']);

             $response = $this->json('POST', '/api/transactions', $transaction->toArray());
             $response->assertStatus(201);
             $this->assertIsObject($response);
             $response->assertJsonStructure([
                'value',
                'status',
                'checking_copy',
                'donations_id'
             ]);

         }catch(Exception $e){
             throw $e;
         }
     }
}
