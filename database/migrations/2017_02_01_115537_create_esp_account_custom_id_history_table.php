<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEspAccountCustomIdHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esp_account_custom_id_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('esp_account_id')->unsigned();
            $table->integer('custom_id')->unsigned();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('esp_account_custom_id_history');
    }
}
