<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListProfileSchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('list_profile')->create('list_profile_schedules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('list_profile_id')->unsigned()->default(0);
			$table->boolean('run_daily')->default(0);
			$table->boolean('run_weekly')->default(0);
			$table->enum('day_of_week', array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday',''));
			$table->boolean('run_monthly')->default(0);
			$table->boolean('day_of_month')->nullable();
			$table->dateTime('last_run')->nullable();
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
		Schema::connection('list_profile')->drop('list_profile_schedules');
	}

}
