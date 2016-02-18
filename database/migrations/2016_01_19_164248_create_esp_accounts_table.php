<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEspAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esp_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_number', 50);
            $table->string('key_1', 100);
            $table->string('key_2', 100);
            $table->integer('esp_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('esp_accounts', function($table) {
            $table->foreign('esp_id')->references('id')->on('esps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('esp_accounts');
    }
}


