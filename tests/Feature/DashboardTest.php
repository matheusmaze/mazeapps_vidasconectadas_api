<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    /** @test */
    public function getCards(){
        $response = $this->get('/api/dashboard/cards_geral');
        $response->assertStatus(200);
        $this->assertArrayHasKey('users', $response, "Array doesn't contains 'users' as key");
        $this->assertArrayHasKey('donations', $response, "Array doesn't contains 'donations' as key");
        $this->assertArrayHasKey('institutions', $response, "Array doesn't contains 'institutions' as key");
        $this->assertIsInt($response['users']);
        $this->assertIsInt($response['donations']);
        $this->assertIsInt($response['institutions']);
        $this->assertIsObject($response);
    }
}
