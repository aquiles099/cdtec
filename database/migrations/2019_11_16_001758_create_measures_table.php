<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeasuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->string('unit', 45)->nullable(); 
            $table->double('lastData', 20,15)->nullable();
            $table->timestamp('lastDataDate')->nullable();
            $table->string('monitoringTime', 45)->nullable();
            $table->string('sensorDepth', 45)->nullable();
            $table->string('depthUnit', 45)->nullable();
            $table->string('sensorType', 45)->nullable();
            $table->string('readType', 45)->nullable();
            $table->string('lastMeasureDataUpdate')->nullable();
            $table->unsignedInteger('id_farm')->nullable();
            $table->unsignedInteger('id_node')->nullable();
            $table->unsignedInteger('id_zone')->nullable();
            $table->unsignedInteger('id_physical_connection')->nullable();
            $table->string('id_wiseconn')->nullable();
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
        Schema::dropIfExists('measures');
    }
}
