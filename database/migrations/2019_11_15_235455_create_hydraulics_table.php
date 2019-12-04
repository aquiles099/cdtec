<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHydraulicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hydraulics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 45)->nullable();
            $table->string('type', 45)->nullable();
            $table->unsignedInteger('id_farm')->nullable();
            $table->unsignedInteger('id_node')->nullable();
            $table->unsignedInteger('id_physical_connection')->nullable();
            $table->unsignedInteger('id_zone')->nullable();         
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
        Schema::dropIfExists('hydraulics');
    }
}
