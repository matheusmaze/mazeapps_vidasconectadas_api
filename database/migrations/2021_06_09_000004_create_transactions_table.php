<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'transactions';

    /**
     * Run the migrations.
     * @table transactions
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('value', 10,2);
            $table->enum('status', ['ABERTO', 'PAGO']);
            $table->unsignedInteger('donations_id');
            $table->timestamps();

            $table->index(["donations_id"], 'fk_transactions_donations1_idx');


            $table->foreign('donations_id', 'fk_transactions_donations1_idx')
                ->references('id')->on('donations')
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
