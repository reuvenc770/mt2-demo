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
            $table->dateTime('last_success_clicks')->nullable();
            $table->dateTime('last_fail_clicks')->nullable();
            $table->dateTime('last_success_opens')->nullable();
            $table->dateTime('last_fail_opens')->nullable();
            $table->dateTime('last_success_deliveres')->nullable();
            $table->dateTime('last_fail_deliveres')->nullable();
            $table->dateTime('last_success_unsubs')->nullable();
            $table->dateTime('last_fail_unsubs')->nullable();
            $table->dateTime('last_success_hardbounce')->nullable();
            $table->dateTime('last_fail_unsubs_hardbounce')->nullable();
            $table->dateTime('last_success_complaints')->nullable();//not used but will be
            $table->dateTime('last_fail_complaints')->nullable();
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
