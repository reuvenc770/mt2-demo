<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignActionsEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_actions_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('esp_account_id');
            $table->integer("esp_internal_id");
            $table->dateTime('last_success_click')->nullable();
            $table->dateTime('last_fail_click')->nullable();
            $table->dateTime('last_success_open')->nullable();
            $table->dateTime('last_fail_open')->nullable();
            $table->dateTime('last_success_deliverable')->nullable();
            $table->dateTime('last_fail_deliverable')->nullable();
            $table->dateTime('last_success_optout')->nullable();
            $table->dateTime('last_fail_optout')->nullable();
            $table->dateTime('last_success_bounce')->nullable();
            $table->dateTime('last_fail_bounce')->nullable();
            $table->dateTime('last_success_complaint')->nullable();//not used but will be
            $table->dateTime('last_fail_complaint')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('campaign_actions_entries');
    }
}
