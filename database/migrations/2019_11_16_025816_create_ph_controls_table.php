<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ph_controls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('setPoint')->default(0);
            $table->integer('preIrrigationSeconds')->default(0);
            $table->integer('postIrrigationSeconds')->default(0);
            $table->integer('phAverage')->default(0);
            $table->integer('CEAverage')->default(0);
            $table->integer('pHInjectorId')->default(0);
            $table->unsignedBigInteger('id_real_irrigation')->unsigned();
            $table->foreign('id_real_irrigation')
                ->references('id')
                ->on('real_irrigations')
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
        Schema::dropIfExists('ph_controls');
    }
}
