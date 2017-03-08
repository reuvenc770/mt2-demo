<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailDirectReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('email_direct_reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('internal_id');
			$table->integer('campaign_id')->nullable();
			$table->string('name')->nullable();
			$table->string('status')->nullable();
			$table->integer('is_active')->nullable();
			$table->dateTime('created')->nullable();
			$table->dateTime('schedule_date')->nullable();
			$table->string('from_name')->nullable();
			$table->string('from_email')->nullable();
			$table->string('to_name')->nullable();
			$table->integer('creative_id')->nullable();
			$table->string('target')->nullable();
			$table->string('subject')->nullable();
			$table->string('archive_url')->nullable();
			$table->integer('emails_sent')->nullable();
			$table->integer('opens')->nullable();
			$table->integer('unique_clicks')->nullable();
			$table->integer('total_clicks')->nullable();
			$table->integer('removes')->nullable();
			$table->integer('forwards')->nullable();
			$table->integer('forwards_from')->nullable();
			$table->integer('hard_bounces')->nullable();
			$table->integer('soft_bounces')->nullable();
			$table->integer('complaints')->nullable();
			$table->integer('delivered')->nullable();
			$table->float('delivery_rate')->nullable();
			$table->float('open_rate')->nullable();
			$table->float('unique_rate')->nullable();
			$table->float('ctr')->nullable();
			$table->float('remove_rate')->nullable();
			$table->float('bounce_rate')->nullable();
			$table->float('soft_bounce_rate')->nullable();
			$table->float('complaint_rate')->nullable();
			$table->timestamps();
			$table->integer('esp_account_id')->unsigned()->index('email_direct_reports_esp_account_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('email_direct_reports');
	}

}
