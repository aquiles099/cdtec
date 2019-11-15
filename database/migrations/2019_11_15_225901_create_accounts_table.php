<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 45);
            $table->string('rut', 45);
            $table->string('razonsocial', 45);
            $table->string('rutlegal', 45);
            $table->string('direccion', 45);
            $table->string('telefono', 45);
            $table->string('email', 45);
            $table->string('comentario', 45);
            $table->string('habilitar', 45);
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
        Schema::dropIfExists('accounts');
    }
}
