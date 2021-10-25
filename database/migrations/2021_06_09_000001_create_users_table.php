<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'users';

    /**
     * Run the migrations.
     * @table users
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45);
            $table->string('email', 70);
            $table->text('password');
            $table->string('document', 20)->unique();
            $table->date('birthday');
            $table->string('phone_number', 20);
            $table->enum('nivel', ['MASTER', 'NEGOCIADOR', 'PADRINHO', 'DOADOR'])->comment('1 Administrador (Acesso geral)
                2 Negociadores (Responsável pelas compras, com os valores arrecadados)
                3 Padrinhos (Cadastra novas pessoas)
                4 Doadores (Usuários comuns)');
            $table->boolean('blood_donator');
            $table->boolean('welcome_email');
            $table->unsignedInteger('users_id')->nullable();
            $table->timestamps();

            $table->index(["users_id"], 'fk_users_users1_idx');


            $table->foreign('users_id', 'fk_users_users1_idx')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
