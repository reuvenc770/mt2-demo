<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBrontoIdMappingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('bronto_id_mappings', function(Blueprint $table)
		{
			$table->string('primary_id')->primary();
			$table->integer('generated_id');
			$table->integer('esp_account_id')->index('generated_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('bronto_id_mappings');
	}

}
