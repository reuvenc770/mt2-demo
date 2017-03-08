<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailCampaignStatisticsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('email_campaign_statistics', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('email_id')->unsigned()->default(0)->index();
			$table->integer('campaign_id')->unsigned()->default(0);
			$table->string('last_status')->default('esp load');
			$table->dateTime('esp_first_open_datetime')->nullable();
			$table->dateTime('esp_last_open_datetime')->nullable();
			$table->integer('esp_total_opens')->unsigned()->default(0);
			$table->dateTime('esp_first_click_datetime')->nullable();
			$table->dateTime('esp_last_click_datetime')->nullable();
			$table->integer('esp_total_clicks')->unsigned()->default(0);
			$table->dateTime('trk_first_open_datetime')->nullable();
			$table->dateTime('trk_last_open_datetime')->nullable();
			$table->integer('trk_total_opens')->unsigned()->default(0);
			$table->dateTime('trk_first_click_datetime')->nullable();
			$table->dateTime('trk_last_click_datetime')->nullable();
			$table->integer('trk_total_clicks')->unsigned()->default(0);
			$table->dateTime('mt_first_open_datetime')->nullable();
			$table->dateTime('mt_last_open_datetime')->nullable();
			$table->integer('mt_total_opens')->unsigned()->default(0);
			$table->dateTime('mt_first_click_datetime')->nullable();
			$table->dateTime('mt_last_click_datetime')->nullable();
			$table->integer('mt_total_clicks')->unsigned()->default(0);
			$table->integer('unsubscribed')->unsigned()->default(0);
			$table->integer('hard_bounce')->unsigned()->default(0);
			$table->timestamps();
			$table->integer('user_agent_id')->unsigned()->default(0)->index('user_agent_id');
			$table->unique(['campaign_id','email_id'], 'campaign_email');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('email_campaign_statistics');
	}

}
