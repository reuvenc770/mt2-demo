<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEspDataExportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esp_data_exports', function(Blueprint $table)
		{
			$table->integer('feed_id')->unsigned()->default(0)->primary();
			$table->integer('esp_account_id')->unsigned()->default(0)->index();
			$table->string('target_list')->default('');
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
		Schema::drop('esp_data_exports');
	}

}
