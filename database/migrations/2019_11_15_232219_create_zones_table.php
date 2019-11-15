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
            $table->string('description', 45);
            $table->string('latitude', 45);
            $table->string('longitude', 45);
            $table->string('type', 45);
            $table->integer('kc')->default(0);
            $table->integer('theoreticalFlow')->default(0);
            $table->string('unitTheoreticalFlow', 45);
            $table->integer('efficiency')->default(0);
            $table->integer('humidityRetention')->default(0);
            $table->integer('max')->default(0);
            $table->integer('min')->default(0);
            $table->integer('criticalPoint1')->default(0);
            $table->integer('criticalPoint2')->default(0);
            $table->unsignedBigInteger('id_farm')->unsigned();
            $table->foreign('id_farm')
                ->references('id')
                ->on('farms')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('id_pump_system')->unsigned();
            $table->foreign('id_pump_system')
                ->references('id')
                ->on('pump_systems')
                ->onDelete('cascade')
                ->onUpdate('cascade');            
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
