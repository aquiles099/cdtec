<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealIrrigationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('real_irrigations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('initTime', 45)->nullable();
            $table->string('endTime', 45)->nullable();
            $table->string('status', 45)->nullable();
            $table->unsignedBigInteger('id_farm')->unsigned();
            $table->foreign('id_farm')
                ->references('id')
                ->on('farms')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('id_irrigation')->nullable();
            $table->integer('id_zone')->nullable();
            $table->integer('id_pump_system')->nullable();   
            $table->integer('id_wiseconn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('real_irrigations');
    }
}
