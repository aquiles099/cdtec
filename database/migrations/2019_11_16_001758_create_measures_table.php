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
            $table->integer('lastData')->nullable();
            $table->timestamp('lastDataDate')->nullable();
            $table->string('monitoringTime', 45)->nullable();
            $table->string('sensorDepth', 45)->nullable();
            $table->string('depthUnit', 45)->nullable();
            $table->string('sensorType', 45)->nullable();
            $table->string('readType', 45)->nullable();
            $table->unsignedBigInteger('id_farm')->unsigned();
            $table->foreign('id_farm')
                ->references('id')
                ->on('farms')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
