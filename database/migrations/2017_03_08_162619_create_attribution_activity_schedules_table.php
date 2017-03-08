<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttributionActivitySchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('attribution')->create('attribution_activity_schedules', function(Blueprint $table)
		{
			$table->bigInteger('email_id')->unsigned()->primary();
			$table->date('trigger_date')->index('attribution_activity_schedules_inactive_date_index');
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
		Schema::connection('attribution')->drop('attribution_activity_schedules');
	}

}
