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
            $table->string('initTime', 45);
            $table->string('endTime', 45);
            $table->string('dilution', 45);
            $table->boolean('soluble')->default(false);
            $table->foreign('id_fertilizer')
                ->references('id')
                ->on('fertilizers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('id_real_irrigation')->unsigned();
            $table->foreign('id_real_irrigation')
                ->references('id')
                ->on('real_irrigations')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('id_volume')->unsigned();
            $table->foreign('id_volume')
                ->references('id')
                ->on('volumes')
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
        Schema::dropIfExists('fertigations');
    }
}
