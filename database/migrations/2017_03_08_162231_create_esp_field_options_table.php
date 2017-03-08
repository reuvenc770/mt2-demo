<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEspFieldOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esp_field_options', function(Blueprint $table)
		{
			$table->integer('esp_id')->primary();
			$table->string('email_id_field')->default('');
			$table->string('open_email_id_field')->default('');
			$table->string('email_address_field')->default('');
			$table->string('open_email_address_field')->default('');
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
		Schema::drop('esp_field_options');
	}

}
