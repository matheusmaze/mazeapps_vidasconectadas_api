<?php

namespace Tests\Feature;

use App\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Exception;
use App\User;

class DonationTest extends TestCase
{
    function __construct()
    {
        parent::setUp();
    }
    /** @test */
    public function createDonation($id)
    {
        try{
            $donation = factory(Donation::class)->make([
                'users_id' => $id
            ]);

            $this->assertIsString($donation['recurrence_day']);

            $this->assertLessThanOrEqual(4, $donation['recurrence_interval']);
            $this->assertGreaterThanOrEqual(1, $donation['recurrence_interval']);

            $this->assertIsString($donation['end_recurrence']);

            $this->assertLessThanOrEqual(2, $donation['notification_type']);
            $this->assertGreaterThanOrEqual(1, $donation['notification_type']);

            $this->assertLessThanOrEqual(3, $donation['payment_form']);
            $this->assertGreaterThanOrEqual(1, $donation['payment_form']);

            $this->assertIsInt($donation['fixed_value']);
            $this->assertIsInt($donation['users_id']);

            $response = $this->json('POST', '/api/donations', $donation->toArray());
            $response->assertStatus(201);
            $this->assertIsObject($response);
            $response->assertJsonStructure([
                'recurrence_day',
                'recurrence_interval',
                'end_recurrence',
                'notification_type',
                'payment_form',
                'fixed_value',
                'users_id'
            ]);

            $transactionTest = new TransactionTest();
            $transactionTest->createTransaction($response['id'], $response['fixed_value']);
        }catch(Exception $e){
            throw $e;
        }
    }
}
