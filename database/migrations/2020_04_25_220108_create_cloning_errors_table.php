<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCloningErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloning_errors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('elements', 80)->nullable();
            $table->text('uri')->nullable();
            $table->string('id_wiseconn')->nullable();
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
        Schema::dropIfExists('cloning_errors');
    }
}
