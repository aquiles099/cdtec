<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 45);
            $table->string('description')->nullable();
            $table->integer('latitude')->nullable();
            $table->integer('longitude')->nullable();
            $table->string('type', 45)->nullable();
            $table->integer('kc')->nullable();
            $table->integer('theoreticalFlow')->nullable();
            $table->string('unitTheoreticalFlow', 45)->nullable();
            $table->integer('efficiency')->nullable();
            $table->integer('humidityRetention')->nullable();
            $table->integer('max')->nullable();
            $table->integer('min')->nullable();
            $table->integer('criticalPoint1')->nullable();
            $table->integer('criticalPoint2')->nullable();
            $table->unsignedInteger('id_farm')->nullable();
            $table->unsignedInteger('id_pump_system')->nullable();
            $table->unsignedInteger('id_wiseconn')->nullable();
            $table->unsignedInteger('id_polygon')->nullable();
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
        Schema::dropIfExists('zones');
    }
}
