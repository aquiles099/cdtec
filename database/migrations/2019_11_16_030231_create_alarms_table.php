<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlarmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alarms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('activationValue')->nullable();
            $table->text('description')->nullable();        
            $table->string('date', 45)->nullable();
            $table->unsignedInteger('id_farm')->nullable();
            $table->unsignedInteger('id_zone')->nullable();
            $table->unsignedInteger('id_irrigation')->nullable();
            $table->unsignedInteger('id_real_irrigation')->nullable();            
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
        Schema::dropIfExists('alarms');
    }
}
