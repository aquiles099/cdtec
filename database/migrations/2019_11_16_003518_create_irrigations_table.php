<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIrrigationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('irrigations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('value')->nullable();
            $table->string('initTime', 45)->nullable();
            $table->string('endTime', 45)->nullable();
            $table->string('status', 45)->nullable();
            $table->boolean('sentToNetwork')->default(false);
            $table->string('scheduledType', 45)->nullable();
            $table->string('groupingName', 45)->nullable();
            $table->string('action', 45)->nullable();            
            $table->integer('id_pump_system')->nullable();
            $table->integer('id_zone')->nullable();
            $table->integer('id_volume')->nullable();
            $table->unsignedBigInteger('id_farm')->unsigned();
            $table->foreign('id_farm')
                ->references('id')
                ->on('farms')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedInteger('id_wiseconn')->nullable();
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
        Schema::dropIfExists('irrigations');
    }
}
