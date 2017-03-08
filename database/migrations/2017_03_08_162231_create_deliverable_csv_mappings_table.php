<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeliverableCsvMappingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('deliverable_csv_mappings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('esp_id')->unsigned()->default(0)->index();
			$table->string('mapping')->default('');
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
		Schema::drop('deliverable_csv_mappings');
	}

}
