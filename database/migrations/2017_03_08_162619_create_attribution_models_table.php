<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttributionModelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('attribution')->create('attribution_models', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->boolean('live');
			$table->boolean('processing')->default(0);
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
		Schema::connection('attribution')->drop('attribution_models');
	}

}
