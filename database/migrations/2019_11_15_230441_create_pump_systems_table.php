<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePumpSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pump_systems', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 45);
            $table->boolean('allowPumpSelection')->default(false);
            $table->unsignedBigInteger('id_farm')->unsigned();
            $table->foreign('id_farm')
                ->references('id')
                ->on('farms')
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
        Schema::dropIfExists('pump_systems');
    }
}
