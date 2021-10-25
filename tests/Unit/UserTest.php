<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function check_register_fields_is_correct()
    {
        //cria a instância
        $user = new User();

        //o que é esperado
        $expected = [
            'name',
            'email',
            'password',
            'document',
            'birthday',
            'phone_number',
            'nivel',
            'blood_donator',
        ];

        //criando o array que vai ser comparado
        $arrayCompared = array_diff($expected, $user->getFillable());
        dd($arrayCompared);
        //Caso sejam iguais, ele retorna 0 e o contador de diferenças
        $this->assertEquals(0, count($arrayCompared));
    }

    /** @test */
    public function create_user_is_working(){
        //Passa um array de dados que serão cadastrados
        $data = [
            'name' => 'teste',
            'email' => 'teste@teste.com',
            'password' => '123123',
            'document' => '123012301230',
            'birthday' => '1999-06-20',
            'phone_number' => '19999033236',
            'nivel' => 'MASTER',
            'blood_donator' => 1,
        ];

        //cria o usuário
        $user = User::create($data);

        //checa se o nome do usuário é igual a teste
        $this->assertEquals($user->name, 'teste');
    }

    /** @test */
    public function check_findUser_is_working(){
        //procura o usuário pelo id
        $found_user = User::find(323);


        $this->assertEquals($found_user->id, 323);
    }

    /** @test */
    public function check_deleteFunction_is_working(){
        //procura o usuário pelo id
        $found_user = User::find(323);
        //faz a exclusão desse mesmo user
        $destroy = User::destroy($found_user->id);
        //retorna 1 se der tudo certo
        $this->assertEmpty($found_user->id);
    }
}
