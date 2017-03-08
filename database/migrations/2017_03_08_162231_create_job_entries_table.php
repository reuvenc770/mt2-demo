<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('job_entries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('job_name');
			$table->string('account_name')->nullable();
			$table->string('account_number')->nullable();
			$table->integer('campaign_id')->default(0);
			$table->timestamp('time_started')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->dateTime('time_finished')->default('0000-00-00 00:00:00');
			$table->integer('rows_impacted')->default(0);
			$table->integer('attempts');
			$table->string('tracking', 16)->index('tracking');
			$table->string('status');
			$table->dateTime('time_fired');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('job_entries');
	}

}
