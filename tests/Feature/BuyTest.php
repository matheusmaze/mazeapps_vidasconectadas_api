<?php

namespace Tests\Feature;

use App\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Exception;
use App\User;
use App\Buy;

class BuyTest extends TestCase
{
    /** @test */
    public function createBuy()
    {
        try{

            $user = factory(User::class)->create();
            $institution = factory(Institution::class)->create();

            $buy = factory(Buy::class)->make([
                'users_id' => $user->id,
                'institutions_id' => $institution->id
            ]);

            $this->assertIsString($buy['description']);
            $this->assertIsInt($buy['value']);
            $this->assertIsString($buy['purchase_voucher']);
            $this->assertIsInt($buy['users_id']);
            $this->assertIsInt($buy['institutions_id']);

            $response = $this->json('POST', '/api/buys', $buy->toArray());
            $response->assertStatus(201);
            $this->assertIsObject($response);

            $response->assertJsonStructure([
                "description",
                "value",
                "purchase_voucher",
                "users_id",
                "institutions_id"
            ]);

        }catch(Exception $e){
            throw $e;
        }
    }
}
