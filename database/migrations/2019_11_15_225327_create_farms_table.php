<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFarmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('farms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 45);
            $table->string('description', 45)->nullable();
            $table->string('latitude', 45)->nullable();
            $table->string('longitude', 45)->nullable();
            $table->string('postalAddress', 45)->nullable();
            $table->string('timeZone', 45)->nullable();
            $table->string('webhook', 45)->nullable();
            $table->unsignedBigInteger('id_account')->unsigned();
            $table->foreign('id_account')
                ->references('id')
                ->on('accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
        Schema::dropIfExists('farms');
    }
}
