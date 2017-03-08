<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFromOpenRatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('from_open_rates', function(Blueprint $table)
		{
			$table->integer('from_id')->unsigned()->default(0);
			$table->integer('list_profile_combine_id')->unsigned()->default(0);
			$table->integer('deploy_id')->unsigned()->default(0);
			$table->integer('delivers')->unsigned()->default(0);
			$table->integer('opens')->unsigned()->default(0);
			$table->timestamps();
			$table->primary(['from_id','list_profile_combine_id','deploy_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('from_open_rates');
	}

}
