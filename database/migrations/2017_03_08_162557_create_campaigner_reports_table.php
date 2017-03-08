<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCampaignerReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('campaigner_reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('internal_id');
			$table->string('name', 50)->nullable();
			$table->string('subject', 150)->nullable();
			$table->string('from_name', 50)->nullable();
			$table->string('from_email', 50)->nullable();
			$table->dateTime('run_on');
			$table->integer('sent')->nullable();
			$table->integer('delivered')->nullable();
			$table->integer('hard_bounces')->nullable();
			$table->integer('soft_bounces')->nullable();
			$table->integer('spam_bounces')->nullable();
			$table->integer('opens')->nullable();
			$table->integer('clicks')->nullable();
			$table->integer('unsubs')->nullable();
			$table->integer('spam_complaints')->nullable();
			$table->timestamps();
			$table->integer('esp_account_id')->unsigned()->index('campaigner_reports_esp_account_id_foreign');
			$table->integer('run_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('campaigner_reports');
	}

}
