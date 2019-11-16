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
            $table->string('initTime', 45);
            $table->string('endTime', 45);
            $table->integer('proportion')->default(0);
            $table->integer('preirrigation')->default(0);
            $table->integer('postirrigation')->default(0);
            $table->unsignedBigInteger('id_fertilizer')->unsigned();
            $table->foreign('id_fertilizer')
                ->references('id')
                ->on('fertilizers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('id_volume')->unsigned();
            $table->foreign('id_volume')
                ->references('id')
                ->on('volumes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('id_irrigation')->unsigned();
            $table->foreign('id_irrigation')
                ->references('id')
                ->on('irrigations')
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
        Schema::dropIfExists('scheduled_fertigations');
    }
}
