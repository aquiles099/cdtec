<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('api_key', 45);
            $table->string('name', 45);
            $table->string('password', 45);
            $table->string('id_user')->unique();
            $table->unsignedBigInteger('id_account')->unsigned();
            $table->foreign('id_account')
                ->references('id')
                ->on('accounts')
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
        Schema::dropIfExists('account_settings');
    }
}
