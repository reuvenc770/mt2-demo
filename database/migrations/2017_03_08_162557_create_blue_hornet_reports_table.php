<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlueHornetReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('blue_hornet_reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('internal_id');
			$table->string('message_subject')->nullable();
			$table->string('message_name', 100)->nullable();
			$table->string('date_sent')->nullable();
			$table->string('message_notes')->nullable();
			$table->integer('withheld_total')->nullable();
			$table->integer('globally_suppressed')->nullable();
			$table->integer('suppressed_total')->nullable();
			$table->integer('bill_codes')->nullable();
			$table->integer('sent_total')->nullable();
			$table->integer('sent_total_html')->nullable();
			$table->integer('sent_total_plain')->nullable();
			$table->float('sent_rate_total', 10, 0)->nullable();
			$table->float('sent_rate_html', 10, 0)->nullable();
			$table->integer('sent_rate_plain')->nullable();
			$table->integer('delivered_total')->nullable();
			$table->integer('delivered_html')->nullable();
			$table->integer('delivered_plain')->nullable();
			$table->float('delivered_rate_total', 10, 0)->nullable();
			$table->float('delivered_rate_html', 10, 0)->nullable();
			$table->float('delivered_rate_plain', 10, 0)->nullable();
			$table->integer('bounced_total')->nullable();
			$table->integer('bounced_html')->nullable();
			$table->integer('bounced_plain')->nullable();
			$table->float('bounced_rate_total', 10, 0)->nullable();
			$table->float('bounced_rate_html', 10, 0)->nullable();
			$table->float('bounced_rate_plain', 10, 0)->nullable();
			$table->integer('invalid_total')->nullable();
			$table->float('invalid_rate_total', 10, 0)->nullable();
			$table->boolean('has_dynamic_content')->nullable();
			$table->boolean('has_delivery_report')->nullable();
			$table->string('link_append_statement')->nullable();
			$table->string('timezone', 50)->nullable();
			$table->string('ftf_forwarded')->nullable();
			$table->string('ftf_signups')->nullable();
			$table->string('ftf_conversion_rate')->nullable();
			$table->integer('optout_total')->nullable();
			$table->float('optout_rate_total', 10, 0)->nullable();
			$table->integer('opened_total')->nullable();
			$table->integer('opened_unique')->nullable();
			$table->float('opened_rate_unique', 10, 0)->nullable();
			$table->float('opened_rate_aps', 10, 0)->nullable();
			$table->integer('clicked_total')->nullable();
			$table->integer('clicked_unique')->nullable();
			$table->float('clicked_rate_unique', 10, 0)->nullable();
			$table->float('clicked_rate_aps', 10, 0)->nullable();
			$table->string('campaign_name', 100)->nullable();
			$table->integer('campaign_id')->nullable();
			$table->timestamps();
			$table->integer('esp_account_id')->unsigned()->index('blue_hornet_reports_esp_account_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('blue_hornet_reports');
	}

}
