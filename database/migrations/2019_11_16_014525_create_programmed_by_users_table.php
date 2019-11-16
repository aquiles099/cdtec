<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgrammedByUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programmed_by_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 45);
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
        Schema::dropIfExists('programmed_by_users');
    }
}
