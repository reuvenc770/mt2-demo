<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEspDataExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esp_data_exports', function (Blueprint $table) {
            $table->integer('feed_id')->unsigned()->default(0);
            $table->integer('esp_account_id')->unsigned()->default(0);
            $table->string('target_list')->default('');
            $table->timestamps();

            $table->primary('feed_id');
            $table->index('esp_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('esp_data_exports');
    }
}
