<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonationsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'donations';

    /**
     * Run the migrations.
     * @table donations
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->date('recurrence_day');
            $table->enum('recurrence_interval', ['MENSAL','BIMESTRAL', 'TRIMESTRAL','SEMESTRAL', 'ANUAL', 'UNICA', 'DIARIA'])->default('MENSAL')->comment('De quanto em quanto tempo a recorr�ncia ser� solicitada.');
            $table->date('end_recurrence');
            $table->enum('notification_type', ['WHATSAPP', 'EMAIL']);
            $table->enum('payment_form', ["CARTAO", "DINHEIRO", "PIX"]);
            $table->decimal('fixed_value', 10, 2)->nullable();
            $table->unsignedInteger('users_id');
            $table->timestamps();
            $table->softDeletes();


            $table->index(["users_id"], 'fk_informacoes_doacoes_users1_idx');


            $table->foreign('users_id', 'fk_informacoes_doacoes_users1_idx')
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
