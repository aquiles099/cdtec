<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');//nombre
            $table->string('last_name');//apellido
            $table->string('business')->nullable();//empresa
            $table->string('office')->nullable();//oficina
            $table->string('password');
            $table->string('email')->unique();//email
            $table->timestamp('email_verified_at')->nullable();
            $table->string('region');//region
            $table->string('city');//ciudad
            $table->string('phone');//telefono            
            $table->unsignedBigInteger('id_role')->unsigned();
            $table->foreign('id_role')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
