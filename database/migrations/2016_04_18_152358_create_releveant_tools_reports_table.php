<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReleveantToolsReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("reporting_data")->create('relevant_tools_reports', function (Blueprint $table) {

            $table->increments('id');
            $table->integer("esp_account_id");
            $table->string("campaign_name");
            $table->integer("total_sent");
            $table->integer("total_open");
            $table->dateTime("datetime");
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
        Schema::drop('releveant_tools_reports');
    }
}
