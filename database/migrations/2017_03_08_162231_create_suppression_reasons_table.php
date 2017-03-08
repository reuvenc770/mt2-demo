<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSuppressionReasonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('suppression_reasons', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('display_status');
			$table->string('legacy_status')->nullable();
			$table->integer('esp_id')->default(0);
			$table->integer('suppression_type')->default(0);
			$table->boolean('display');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('suppression_reasons');
	}

}
