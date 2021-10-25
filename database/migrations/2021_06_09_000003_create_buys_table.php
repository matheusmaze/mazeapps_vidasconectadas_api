<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuysTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'buys';

    /**
     * Run the migrations.
     * @table buys
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->text('description');
            $table->decimal('value', 10,2);
            $table->unsignedInteger('users_id');
            $table->unsignedInteger('institutions_id');
            $table->timestamps();

            $table->index(["users_id"], 'fk_compras_users1_idx');

            $table->index(["institutions_id"], 'fk_buys_institutions1_idx');


            $table->foreign('users_id', 'fk_compras_users1_idx')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('institutions_id', 'fk_buys_institutions1_idx')
                ->references('id')->on('institutions')
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
