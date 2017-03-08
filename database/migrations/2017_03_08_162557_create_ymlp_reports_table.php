<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateYmlpReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('ymlp_reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('esp_account_id')->unsigned()->default(0);
			$table->integer('internal_id')->default(0)->index();
			$table->string('name')->default('');
			$table->string('from_name')->default('');
			$table->string('from_email')->default('');
			$table->string('subject')->default('');
			$table->dateTime('date');
			$table->string('groups')->default('');
			$table->string('filters')->default('');
			$table->integer('recipients')->unsigned()->default(0);
			$table->integer('delivered')->unsigned()->default(0);
			$table->integer('bounced')->unsigned()->default(0);
			$table->integer('total_opens')->unsigned()->default(0);
			$table->integer('unique_opens')->unsigned()->default(0);
			$table->integer('total_clicks')->unsigned()->default(0);
			$table->integer('unique_clicks')->unsigned()->default(0);
			$table->decimal('open_rate', 3)->unsigned()->default(0.00);
			$table->decimal('click_through_rate', 3)->unsigned()->default(0.00);
			$table->string('forwards')->default('');
			$table->string('permalink')->default('');
			$table->timestamps();
			$table->index(['esp_account_id','internal_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('ymlp_reports');
	}

}
