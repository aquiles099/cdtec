<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledFertigationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_fertigations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('initTime', 45)->nullable();
            $table->string('endTime', 45)->nullable();
            $table->integer('proportion')->nullable(0);
            $table->integer('preirrigation')->nullable(0);
            $table->integer('postirrigation')->nullable(0);            
            $table->unsignedInteger('id_fertilizer')->nullable(); 
            $table->unsignedInteger('id_volume')->nullable(); 
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
        Schema::dropIfExists('scheduled_fertigations');
    }
}
