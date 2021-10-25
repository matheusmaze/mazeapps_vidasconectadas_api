<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Tests\Feature\DonationTest;

class UserTest extends TestCase
{
    //Retorna todos os usuários
    /** @test */
    public function getAllUsers(){
        try{
            $response = $this->json('GET', '/api/users');
            $response->assertStatus(200);

            $response->assertJsonStructure([
                [
                'name',
                'email',
                'document',
                'birthday',
                'phone_number',
                'nivel',
                'blood_donator',
                'users_id',
                ]
            ]);

            dd($response);
        }catch(Exception $e){
            throw $e;
        }
    }

    //Retorna um usuário de acordo com o id
    /** @test */
    public function getUser($id){
        try{
            $response = $this->get('/api/users/'. $id);
            $response->assertStatus(200);
            $this->assertIsObject($response);

            $response->assertJsonStructure([
                'name',
                'email',
                'document',
                'birthday',
                'phone_number',
                'nivel',
                'blood_donator',
                'users_id',
            ]);

        }catch(Exception $e){
            throw $e;
        }
    }

    //Retorna os usuários vinculados a outro usuário
    /** @test */
    public function getUserByUserId(){
        try{
            $response = $this->get('/api/users/users/300');
            $response->assertStatus(200);
        }catch(Exception $e){
            throw $e;
        }
    }

    //função que cria o usuário
    /** @test */
    public function createUser(){
        try{
            $user = factory(User::class)->make();
            $this->assertIsString($user['name']);
            $this->assertIsString($user['email']);
            $this->assertIsString($user['document']);
            $this->assertLessThanOrEqual(14, strlen($user['document']));
            $this->assertGreaterThanOrEqual(11, strlen($user['document']));
            $this->assertNotEmpty($user['birthday']);
            $this->assertLessThanOrEqual(14, strlen($user['phone_number']));
            $this->assertGreaterThanOrEqual(10, strlen($user['phone_number']));
            $this->assertNotEmpty($user['nivel']);
            $this->assertLessThanOrEqual(3, $user['nivel']);
            $this->assertGreaterThanOrEqual(1, $user['nivel']);
            $this->assertIsBool($user['blood_donator']);

            $response = $this->json('POST', '/api/register', $user->toArray());
            $this->assertIsObject($response);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'user' => [
                    'name',
                    'email',
                    'document',
                    'birthday',
                    'phone_number',
                    'nivel',
                    'blood_donator',
                ],
                'token'
            ]);
            $this->getUser($response['user']['id']);

            //chamando a função de criação de Doação passando o Id do usuário
            $donationTest = new DonationTest();
            $donationTest->createDonation($response['user']['id']);
        }catch(Exception $e){
            throw $e;
        }
    }

    //Atualizar o usuário de acordo com o id
    /** @test */
    public function updateUser(){
        try{
            $data = [
                'name' => 'Vinicius Soares',
                'password' => 'vini12344',
                'nivel' => 'PADRINHO',
            ];
            $response = $this->put('/api/users/381', $data);
            $response->assertStatus(201);

        }catch(Exception $e){
            throw $e;
        }
    }

    //Função para deletar usuário de acordo com o id
    /** @test */
    public function deleteUser($id){
        try{
            $response = $this->delete('/api/users/' .$id);
            $response->assertStatus(200);

        }catch(Exception $e){
            throw $e;
        }
    }

    /** @test */
    public function login(){
        try{
            $data = [
                'document' => ' 12345678901',
                'password' => '123123',
            ];

            $this->assertIsString($data['document']);
            $this->assertIsString($data['password']);

            $response = $this->json('POST', '/api/login', $data);
            $response->assertStatus(200);
            $response->assertJsonStructure([
                "token"
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }

      /** @test */
      public function loginFailed(){
        try{
            $data = [
                'document' => ' 12356666666',
                'password' => '123123',
            ];

            $this->assertIsString($data['document']);
            $this->assertIsString($data['password']);
            $response = $this->json('POST', '/api/login', $data);
            $response->assertStatus(400);
            $response->assertJsonStructure([
                "message",
                "data"
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }
}
