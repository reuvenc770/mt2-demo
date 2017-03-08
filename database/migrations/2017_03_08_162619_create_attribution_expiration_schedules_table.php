<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttributionExpirationSchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('attribution')->create('attribution_expiration_schedules', function(Blueprint $table)
		{
			$table->bigInteger('email_id')->unsigned()->primary();
			$table->date('trigger_date')->index('attribution_expiration_schedules_expiration_date_index');
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
		Schema::connection('attribution')->drop('attribution_expiration_schedules');
	}

}
