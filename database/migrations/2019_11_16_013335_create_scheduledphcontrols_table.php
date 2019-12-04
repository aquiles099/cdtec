<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledphcontrolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduledphcontrols', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('setPoint')->nullable();
            $table->integer('preIrrigationSeconds')->nullable();
            $table->integer('postIrrigationSeconds')->nullable();
            $table->integer('phAverage')->nullable();
            $table->integer('CEAverage')->nullable();
            $table->integer('pHInjectorId')->nullable();
            $table->unsignedInteger('id_irrigation')->nullable();
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
        Schema::dropIfExists('scheduledphcontrols');
    }
}
