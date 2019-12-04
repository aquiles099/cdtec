<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFertigationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fertigations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('initTime', 45)->nullable();
            $table->string('endTime', 45)->nullable();
            $table->string('dilution', 45)->nullable();
            $table->boolean('soluble')->nullable();
            $table->unsignedInteger('id_fertilizer')->nullable();
            $table->unsignedInteger('id_real_irrigation')->nullable();
            $table->unsignedInteger('id_volume')->nullable();
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
        Schema::dropIfExists('fertigations');
    }
}
