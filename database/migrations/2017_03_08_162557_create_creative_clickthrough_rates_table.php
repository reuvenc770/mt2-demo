<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCreativeClickthroughRatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('creative_clickthrough_rates', function(Blueprint $table)
		{
			$table->integer('creative_id')->unsigned()->default(0);
			$table->integer('list_profile_combine_id')->unsigned()->default(0);
			$table->integer('deploy_id')->unsigned()->default(0);
			$table->integer('delivers')->default(0);
			$table->integer('opens')->unsigned()->default(0);
			$table->integer('clicks')->unsigned()->default(0);
			$table->timestamps();
			$table->primary(['creative_id','list_profile_combine_id','deploy_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('creative_clickthrough_rates');
	}

}
