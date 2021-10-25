<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('users_id')->nullable();
            $table->string('controller', 255)->nullable();
            $table->string('uri', 255)->nullable();
            $table->longText('parametros')->nullable();
            $table->longText('body')->nullable();
            $table->string('metodo', 25)->nullable();
            $table->timestamps();

            $table->index(["users_id"], 'fk_logs_users_idx');
            $table->foreign('users_id', 'fk_logs_users_idx')
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
        Schema::dropIfExists('logs');
    }
}
