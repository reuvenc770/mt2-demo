<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMaroReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('maro_reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('esp_account_id')->unsigned()->default(0);
			$table->integer('internal_id')->default(0)->index();
			$table->string('name')->default('');
			$table->string('status')->default('');
			$table->integer('sent')->default(0);
			$table->integer('delivered')->default(0);
			$table->integer('open')->default(0);
			$table->integer('click')->default(0);
			$table->integer('bounce')->default(0);
			$table->dateTime('send_at')->nullable()->default('0000-00-00 00:00:00');
			$table->dateTime('sent_at')->nullable()->default('0000-00-00 00:00:00');
			$table->dateTime('maro_created_at')->nullable()->default('0000-00-00 00:00:00');
			$table->dateTime('maro_updated_at')->nullable()->default('0000-00-00 00:00:00');
			$table->timestamps();
			$table->string('from_name')->default('');
			$table->string('from_email')->default('');
			$table->string('subject')->default('');
			$table->integer('unique_opens')->unsigned()->default(0);
			$table->integer('unique_clicks')->unsigned()->default(0);
			$table->integer('unsubscribes')->unsigned()->default(0);
			$table->integer('complaints')->unsigned()->default(0);
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
		Schema::connection('reporting_data')->drop('maro_reports');
	}

}
