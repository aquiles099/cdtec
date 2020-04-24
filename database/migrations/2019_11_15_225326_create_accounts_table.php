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
            $table->text('name');
            $table->string('rut', 45)->nullable();
            $table->string('razonsocial', 45)->nullable();
            $table->string('rutlegal', 45)->nullable();
            $table->string('direccion', 45)->nullable();
            $table->string('telefono', 45)->nullable();
            $table->string('email', 45)->nullable();
            $table->string('comentario', 45)->nullable();
            $table->string('habilitar', 45)->nullable();
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
        Schema::dropIfExists('accounts');
    }
}
