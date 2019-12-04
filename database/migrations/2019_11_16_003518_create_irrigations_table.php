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
            $table->unsignedInteger('id_pump_system')->nullable();
            $table->unsignedInteger('id_zone')->nullable();
            $table->unsignedInteger('id_volume')->nullable();
            $table->unsignedInteger('id_farm')->nullable();
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
