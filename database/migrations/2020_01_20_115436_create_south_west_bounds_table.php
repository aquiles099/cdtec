<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSouthWestBoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('south_west_bounds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_zone')->nullable();
            $table->double('lat', 15, 10)->nullable();
            $table->double('lng', 15, 10)->nullable();
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
        Schema::dropIfExists('south_west_bounds');
    }
}
