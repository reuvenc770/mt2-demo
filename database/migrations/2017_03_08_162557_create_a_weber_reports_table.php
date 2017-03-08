<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAWeberReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('a_weber_reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('esp_account_id')->unsigned();
			$table->string('deploy_id')->nullable();
			$table->string('campaign_name');
			$table->string('subject', 100)->nullable();
			$table->string('internal_id')->nullable()->unique();
			$table->string('info_url', 100)->nullable();
			$table->integer('total_sent')->nullable();
			$table->integer('total_opens')->nullable();
			$table->integer('unique_opens')->nullable();
			$table->integer('total_clicks')->nullable();
			$table->integer('unique_clicks')->nullable();
			$table->integer('total_unsubscribes')->nullable();
			$table->integer('total_undelivered')->nullable();
			$table->dateTime('datetime')->nullable();
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
		Schema::connection('reporting_data')->drop('a_weber_reports');
	}

}
