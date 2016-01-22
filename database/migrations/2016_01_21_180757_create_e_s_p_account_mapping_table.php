<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateESPAccountMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esp_account_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->string("mappings");
            $table->integer('esp_account_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('esp_account_mappings', function($table) {
            $table->foreign('esp_account_id')->references('id')->on('esp_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('e_s_p_account_mappings');
    }
}
