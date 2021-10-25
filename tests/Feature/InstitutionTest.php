<?php

namespace Tests\Feature;

use App\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Exception;

class InstitutionTest extends TestCase
{
    /** @test */
    public function createInstitution()
    {
        try{
            $institution = factory(Institution::class)->make();

            $this->assertIsString($institution['name']);
            $this->assertIsInt($institution['code']);
            $this->assertIsString($institution['logo']);
            $this->assertIsString($institution['cnpj']);
            $this->assertLessThanOrEqual(20, strlen($institution['cnpj']));
            $this->assertGreaterThanOrEqual(13, strlen($institution['cnpj']));
            $this->assertIsString($institution['email']);
            $this->assertLessThanOrEqual(20, strlen($institution['phone_number']));
            $this->assertGreaterThanOrEqual(10, strlen($institution['phone_number']));
            $this->assertIsString($institution['redirect_link']);

            $response = $this->json('POST', '/api/institutions', $institution->toArray());
            $response->assertStatus(201);
            $this->assertIsObject($response);

            $response->assertJsonStructure([
                'name',
                'code' ,
                'logo',
                'cnpj',
                'email',
                'phone_number',
                'redirect_link'
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }
}
