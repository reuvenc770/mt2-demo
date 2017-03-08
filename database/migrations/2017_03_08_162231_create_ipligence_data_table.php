<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIpligenceDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ipligence_data', function(Blueprint $table)
		{
			$table->integer('ip_from')->unsigned()->default(0000000000);
			$table->integer('ip_to')->unsigned()->default(0000000000)->primary();
			$table->string('country_code', 10);
			$table->string('country_name');
			$table->string('continent_code', 10);
			$table->string('continent_name');
			$table->string('time_zone', 10);
			$table->string('region_code', 10);
			$table->string('region_name');
			$table->string('owner');
			$table->string('city_name');
			$table->string('county_name');
			$table->string('post_code', 10);
			$table->string('metro_code', 10);
			$table->string('area_code', 10);
			$table->float('latitude', 10, 0);
			$table->float('longitude', 10, 0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ipligence_data');
	}

}
